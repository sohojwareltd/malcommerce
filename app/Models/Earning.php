<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    protected $fillable = [
        'sponsor_id',
        'referral_id',
        'order_id',
        'earning_type',
        'comment',
        'amount',
        'platform_revenue',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_revenue' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referral_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}



