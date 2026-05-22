<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalCourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'digital_course_id',
        'digital_course_order_id',
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

    public function course(): BelongsTo
    {
        return $this->belongsTo(DigitalCourse::class, 'digital_course_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(DigitalCourseOrder::class, 'digital_course_order_id');
    }

    public static function grantForOrder(DigitalCourseOrder $order): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $order->user_id,
                'digital_course_id' => $order->digital_course_id,
            ],
            [
                'digital_course_order_id' => $order->id,
                'granted_at' => now(),
            ]
        );
    }
}
