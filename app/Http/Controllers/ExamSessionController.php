<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ExamAccessService;
use App\Services\ExamAttemptService;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    public function start(Exam $exam, ExamAccessService $accessService, ExamAttemptService $attemptService)
    {
        if (!$exam->is_active) {
            abort(404);
        }

        if ($exam->questions()->count() === 0) {
            return redirect()->route('exams.show', $exam)
                ->with('error', 'This exam has no questions yet.');
        }

        $check = $accessService->canStartExam(auth()->user(), $exam);

        if (!$check['allowed']) {
            if (!empty($check['passed'])) {
                return redirect()->route('exams.show', $exam)->with('info', $check['message']);
            }

            return redirect()->route('exams.show', $exam)->with('error', $check['message']);
        }

        if (!empty($check['resume'])) {
            return redirect()->route('exams.take', $check['attempt']);
        }

        $attempt = $attemptService->start(auth()->user(), $exam, $check['enrollment']);

        return redirect()->route('exams.take', $attempt);
    }

    public function take(ExamAttempt $attempt, ExamAttemptService $attemptService)
    {
        if (!$attemptService->authorizeAttempt(auth()->user(), $attempt)) {
            if ($attempt->user_id === auth()->id() && $attempt->status === ExamAttempt::STATUS_COMPLETED) {
                return redirect()->route('exams.result', $attempt);
            }

            abort(403);
        }

        if ($attempt->isExpired()) {
            app(ExamAttemptService::class)->submit($attempt, []);

            return redirect()->route('exams.result', $attempt)
                ->with('error', 'Time expired. Your exam was auto-submitted.');
        }

        $questions = $attemptService->questionsForAttempt($attempt);

        return view('exams.take', compact('attempt', 'questions'));
    }

    public function submit(Request $request, ExamAttempt $attempt, ExamAttemptService $attemptService)
    {
        if (!$attemptService->authorizeAttempt(auth()->user(), $attempt)) {
            abort(403);
        }

        $answers = $request->input('answers', []);
        $attemptService->submit($attempt, is_array($answers) ? $answers : []);

        return redirect()->route('exams.result', $attempt);
    }

    public function result(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $attempt->load(['exam', 'certificate']);

        return view('exams.result', compact('attempt'));
    }

    public function myCertificates()
    {
        $certificates = auth()->user()
            ->examCertificates()
            ->with('exam')
            ->orderByDesc('issued_at')
            ->get();

        return view('exams.my-certificates', compact('certificates'));
    }
}
