<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamEnrollment;
use App\Models\ExamQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamAttemptService
{
    public function __construct(protected ExamAccessService $accessService)
    {
    }

    public function start(User $user, Exam $exam, ExamEnrollment $enrollment): ExamAttempt
    {
        $exam->loadMissing('questions');

        $expiresAt = $exam->duration_minutes
            ? now()->addMinutes($exam->duration_minutes)
            : null;

        return ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'exam_enrollment_id' => $enrollment->id,
            'started_at' => now(),
            'expires_at' => $expiresAt,
            'total_questions' => $exam->questions->count(),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);
    }

    public function submit(ExamAttempt $attempt, array $answers): ExamAttempt
    {
        if (!$attempt->isInProgress()) {
            return $attempt;
        }

        if ($attempt->isExpired()) {
            $attempt->update([
                'status' => ExamAttempt::STATUS_EXPIRED,
                'finished_at' => now(),
            ]);

            return $attempt->fresh();
        }

        $attempt->load(['exam.questions.options']);

        return DB::transaction(function () use ($attempt, $answers) {
            $correct = 0;
            $total = $attempt->exam->questions->count();

            foreach ($attempt->exam->questions as $question) {
                $selectedOptionId = isset($answers[$question->id]) ? (int) $answers[$question->id] : null;
                $selectedOption = $selectedOptionId
                    ? $question->options->firstWhere('id', $selectedOptionId)
                    : null;

                $isCorrect = $selectedOption?->is_correct ?? false;
                if ($isCorrect) {
                    $correct++;
                }

                ExamAnswer::updateOrCreate(
                    [
                        'exam_attempt_id' => $attempt->id,
                        'exam_question_id' => $question->id,
                    ],
                    [
                        'exam_question_option_id' => $selectedOption?->id,
                        'is_correct' => $isCorrect,
                    ]
                );
            }

            $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;
            $passed = $score >= $attempt->exam->pass_mark;

            $attempt->update([
                'correct_answers' => $correct,
                'score' => $score,
                'passed' => $passed,
                'status' => ExamAttempt::STATUS_COMPLETED,
                'finished_at' => now(),
            ]);

            if ($passed && $attempt->exam->certificate_enabled) {
                app(CertificateService::class)->issue($attempt->fresh());
            }

            return $attempt->fresh(['exam', 'certificate']);
        });
    }

    public function authorizeAttempt(User $user, ExamAttempt $attempt): bool
    {
        return $attempt->user_id === $user->id && $attempt->isInProgress();
    }

    public function questionsForAttempt(ExamAttempt $attempt)
    {
        return ExamQuestion::with('options')
            ->where('exam_id', $attempt->exam_id)
            ->orderBy('sort_order')
            ->get();
    }
}
