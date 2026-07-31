<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ExamCertificate extends Model
{
    protected $fillable = [
        'verification_code',
        'user_id',
        'exam_id',
        'exam_attempt_id',
        'student_name',
        'exam_title',
        'score',
        'pass_mark',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'pass_mark' => 'integer',
            'issued_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }
}
