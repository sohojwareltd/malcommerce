<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DigitalCourseOrder extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'digital_course_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'unit_price',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'payment_transaction_id',
        'payment_invoice_id',
        'payment_response',
        'payment_completed_at',
        'sponsor_id',
        'referral_code',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'payment_completed_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (DigitalCourseOrder $order) {
            if (empty($order->order_number)) {
                do {
                    $candidate = 'DC-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
                } while (self::where('order_number', $candidate)->exists());

                $order->order_number = $candidate;
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(DigitalCourse::class, 'digital_course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function enrollment(): HasOne
    {
        return $this->hasOne(DigitalCourseEnrollment::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'completed' && $this->status === self::STATUS_COMPLETED;
    }
}
