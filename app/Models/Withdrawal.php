<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_INQUIRY = 'inquiry';

    protected $fillable = [
        'sponsor_id',
        'amount',
        'receiving_account_information',
        'status',
        'inquiry_note',
        'admin_note',
        'requested_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'receiving_account_information' => 'array',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public static function activeStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_INQUIRY,
        ];
    }
}



