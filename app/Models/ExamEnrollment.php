<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamEnrollment extends Model
{
    public const ACCESS_FREE = 'free';

    public const ACCESS_PURCHASE = 'purchase';

    public const ACCESS_CODE = 'code';

    public const ACCESS_ADMIN = 'admin';

    protected $fillable = [
        'user_id',
        'exam_id',
        'access_type',
        'exam_order_id',
        'exam_access_code_id',
        'granted_at',
    ];

    protected function casts(): array
    {
        return [
            'granted_at' => 'datetime',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(ExamOrder::class, 'exam_order_id');
    }

    public function accessCode(): BelongsTo
    {
        return $this->belongsTo(ExamAccessCode::class, 'exam_access_code_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public static function grant(User $user, Exam $exam, string $accessType, ?ExamOrder $order = null, ?ExamAccessCode $code = null): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $user->id,
                'exam_id' => $exam->id,
            ],
            [
                'access_type' => $accessType,
                'exam_order_id' => $order?->id,
                'exam_access_code_id' => $code?->id,
                'granted_at' => now(),
            ]
        );
    }

    public static function grantForOrder(ExamOrder $order): self
    {
        return self::grant(
            $order->user,
            $order->exam,
            self::ACCESS_PURCHASE,
            $order
        );
    }
}
