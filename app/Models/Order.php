<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'order_number',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'delivery_charge',
        'discount',
        'additional_fees',
        'customer_name',
        'customer_email',
        'customer_phone',
        'district',
        'upazila',
        'city_village',
        'post_code',
        'address',
        'user_id',
        'sponsor_id',
        'referral_code',
        'status',
        'notes',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'payment_invoice_id',
        'payment_response',
        'payment_completed_at',
        'steadfast_consignment_id',
        'steadfast_tracking_code',
        'steadfast_delivery_status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'discount' => 'decimal:2',
            'additional_fees' => 'decimal:2',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class)->latest();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Whether the customer can access digital content (paid or order processed).
     */
    public function canAccessDigitalContent(): bool
    {
        if (!$this->product || !$this->product->is_digital) {
            return false;
        }
        if ($this->payment_method === 'bkash') {
            return $this->payment_status === 'completed';
        }
        return in_array($this->status, ['processing', 'shipped', 'delivered'], true);
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                // Generate a unique 6-digit order number with collision check
                do {
                    $candidate = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
                } while (self::where('order_number', $candidate)->exists());

                $order->order_number = $candidate;
            }
        });
    }
}
