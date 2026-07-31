<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\ExamCertificate;

class CertificateService
{
    public function issue(ExamAttempt $attempt): ExamCertificate
    {
        $attempt->loadMissing(['user', 'exam']);

        return ExamCertificate::updateOrCreate(
            [
                'user_id' => $attempt->user_id,
                'exam_id' => $attempt->exam_id,
            ],
            [
                'verification_code' => ExamCertificate::generateVerificationCode(),
                'exam_attempt_id' => $attempt->id,
                'student_name' => $attempt->user->name,
                'exam_title' => $attempt->exam->title,
                'score' => $attempt->score,
                'pass_mark' => $attempt->exam->pass_mark,
                'issued_at' => now(),
            ]
        );
    }

    public function findByVerificationCode(string $code): ?ExamCertificate
    {
        return ExamCertificate::with(['user', 'exam'])
            ->where('verification_code', strtoupper(trim($code)))
            ->first();
    }
}
