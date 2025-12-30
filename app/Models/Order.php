<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
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
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                // Get all order numbers and find the highest numeric one
                $orderNumbers = self::pluck('order_number')->toArray();
                $maxNumber = 0;
                
                foreach ($orderNumbers as $orderNum) {
                    if ($orderNum && is_numeric($orderNum) && strlen($orderNum) <= 6) {
                        $num = (int) $orderNum;
                        if ($num > $maxNumber && $num <= 999999) {
                            $maxNumber = $num;
                        }
                    }
                }
                
                // Start from 1 if no numeric orders found, otherwise increment
                $nextNumber = $maxNumber + 1;
                
                // Ensure we don't exceed 999999
                if ($nextNumber > 999999) {
                    $nextNumber = 1;
                }
                
                // Generate 6-digit order number (000001 to 999999)
                $order->order_number = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
