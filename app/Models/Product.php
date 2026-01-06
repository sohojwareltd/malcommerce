<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'cashback_amount',
        'commission_type',
        'commission_value',
        'compare_at_price',
        'sku',
        'stock_quantity',
        'in_stock',
        'images',
        'page_layout',
        'is_active',
        'is_featured',
        'sort_order',
        'order_form_title',
        'order_button_text',
        'order_min_quantity',
        'order_max_quantity',
        'order_custom_charge',
        'is_free',
        'order_delivery_options',
        'order_hide_summary',
        'order_hide_quantity',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cashback_amount' => 'decimal:2',
            'commission_value' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'in_stock' => 'boolean',
            'images' => 'array',
            'page_layout' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'order_min_quantity' => 'integer',
            'order_max_quantity' => 'integer',
            'order_custom_charge' => 'decimal:2',
            'order_hide_summary' => 'boolean',
            'order_hide_quantity' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getMainImageAttribute()
    {
        $images = $this->images ?? [];
        return !empty($images) ? $images[0] : null;
    }
}
