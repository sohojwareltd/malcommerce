<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamCodeAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'wrong_attempts',
        'banned_at',
    ];

    protected function casts(): array
    {
        return [
            'wrong_attempts' => 'integer',
            'banned_at' => 'datetime',
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

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    public static function forUserExam(int $userId, int $examId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'exam_id' => $examId],
            ['wrong_attempts' => 0]
        );
    }
}
