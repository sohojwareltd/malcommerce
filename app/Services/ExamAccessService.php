<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAccessCode;
use App\Models\ExamAttempt;
use App\Models\ExamCertificate;
use App\Models\ExamCodeAttempt;
use App\Models\ExamEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamAccessService
{
    public function enrollment(User $user, Exam $exam): ?ExamEnrollment
    {
        return ExamEnrollment::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->first();
    }

    public function hasEnrollment(User $user, Exam $exam): bool
    {
        return $this->enrollment($user, $exam) !== null;
    }

    public function isCodeBanned(User $user, Exam $exam): bool
    {
        $record = ExamCodeAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->first();

        return $record?->isBanned() ?? false;
    }

    public function remainingCodeAttempts(User $user, Exam $exam): int
    {
        if (!$exam->requiresCode()) {
            return 0;
        }

        $record = ExamCodeAttempt::forUserExam($user->id, $exam->id);

        if ($record->isBanned()) {
            return 0;
        }

        return max(0, $exam->max_code_attempts - $record->wrong_attempts);
    }

    public function recordWrongCode(User $user, Exam $exam): ExamCodeAttempt
    {
        $record = ExamCodeAttempt::forUserExam($user->id, $exam->id);

        if ($record->isBanned()) {
            return $record;
        }

        $record->increment('wrong_attempts');
        $record->refresh();

        if ($record->wrong_attempts >= $exam->max_code_attempts) {
            $record->update(['banned_at' => now()]);
        }

        return $record;
    }

    public function redeemCode(User $user, Exam $exam, string $code): array
    {
        if ($this->isCodeBanned($user, $exam)) {
            return ['success' => false, 'message' => 'You are banned from entering access codes for this exam. Contact admin.'];
        }

        if ($this->hasEnrollment($user, $exam)) {
            return ['success' => true, 'message' => 'You already have access to this exam.', 'enrollment' => $this->enrollment($user, $exam)];
        }

        $normalized = preg_replace('/\D/', '', $code);
        if (strlen($normalized) !== 6) {
            $this->recordWrongCode($user, $exam);

            return ['success' => false, 'message' => 'Invalid access code.', 'remaining' => $this->remainingCodeAttempts($user, $exam)];
        }

        $accessCode = ExamAccessCode::where('exam_id', $exam->id)
            ->where('code', $normalized)
            ->first();

        if (!$accessCode || !$accessCode->isValid()) {
            $this->recordWrongCode($user, $exam);

            return ['success' => false, 'message' => 'Invalid or expired access code.', 'remaining' => $this->remainingCodeAttempts($user, $exam)];
        }

        return DB::transaction(function () use ($user, $exam, $accessCode) {
            $accessCode->redeem();
            $enrollment = ExamEnrollment::grant($user, $exam, ExamEnrollment::ACCESS_CODE, null, $accessCode);

            return ['success' => true, 'message' => 'Access granted.', 'enrollment' => $enrollment];
        });
    }

    public function grantFreeAccess(User $user, Exam $exam): ExamEnrollment
    {
        return ExamEnrollment::grant($user, $exam, ExamEnrollment::ACCESS_FREE);
    }

    public function ensureAccessForStart(User $user, Exam $exam): array
    {
        if ($this->hasEnrollment($user, $exam)) {
            return ['success' => true, 'enrollment' => $this->enrollment($user, $exam)];
        }

        if ($exam->allowsFreeStart()) {
            return ['success' => true, 'enrollment' => $this->grantFreeAccess($user, $exam)];
        }

        if ($exam->requiresPayment() && ExamEnrollment::where('user_id', $user->id)->where('exam_id', $exam->id)->exists()) {
            return ['success' => true, 'enrollment' => $this->enrollment($user, $exam)];
        }

        return ['success' => false, 'message' => 'You do not have access to this exam.'];
    }

    public function completedAttemptsCount(User $user, Exam $exam): int
    {
        return ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->whereIn('status', [ExamAttempt::STATUS_COMPLETED, ExamAttempt::STATUS_EXPIRED])
            ->count();
    }

    public function hasPassed(User $user, Exam $exam): bool
    {
        return ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('passed', true)
            ->exists();
    }

    public function remainingExamAttempts(User $user, Exam $exam): int
    {
        if ($this->hasPassed($user, $exam)) {
            return 0;
        }

        $used = $this->completedAttemptsCount($user, $exam);

        return max(0, $exam->max_exam_attempts - $used);
    }

    public function canStartExam(User $user, Exam $exam): array
    {
        if ($this->isCodeBanned($user, $exam) && !$this->hasEnrollment($user, $exam)) {
            return ['allowed' => false, 'message' => 'You are banned from accessing this exam. Contact admin.'];
        }

        $access = $this->ensureAccessForStart($user, $exam);
        if (!$access['success']) {
            return ['allowed' => false, 'message' => $access['message']];
        }

        if ($this->hasPassed($user, $exam)) {
            return ['allowed' => false, 'message' => 'You have already passed this exam.', 'passed' => true];
        }

        $inProgress = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
            ->first();

        if ($inProgress) {
            return ['allowed' => true, 'enrollment' => $access['enrollment'], 'attempt' => $inProgress, 'resume' => true];
        }

        if ($this->remainingExamAttempts($user, $exam) <= 0) {
            return ['allowed' => false, 'message' => 'You have used all exam attempts.'];
        }

        return ['allowed' => true, 'enrollment' => $access['enrollment']];
    }

    public function certificate(User $user, Exam $exam): ?ExamCertificate
    {
        return ExamCertificate::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->first();
    }

    public function unbanCode(User $user, Exam $exam): void
    {
        ExamCodeAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->update(['wrong_attempts' => 0, 'banned_at' => null]);
    }
}
