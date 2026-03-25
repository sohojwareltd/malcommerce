<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
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
        'is_digital',
        'digital_content_type',
        'digital_file_path',
        'digital_link_text',
        'only_on_categories',
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
        'sms_templates',
        'payment_options',
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
            'is_digital' => 'boolean',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'order_min_quantity' => 'integer',
            'order_max_quantity' => 'integer',
            'order_custom_charge' => 'decimal:2',
            'order_hide_summary' => 'boolean',
            'order_hide_quantity' => 'boolean',
            'sms_templates' => 'array',
            'payment_options' => 'array',
        ];
    }

    /**
     * Get allowed payment methods for this product
     * Returns array of allowed payment methods, or ['cod', 'bkash'] if not set (all methods allowed)
     */
    public function getAllowedPaymentMethods(): array
    {
        $paymentOptions = $this->payment_options;
        
        // If null or empty, allow all payment methods
        if (empty($paymentOptions) || !is_array($paymentOptions)) {
            return ['cod', 'bkash'];
        }
        
        // Return the allowed payment methods
        return $paymentOptions;
    }

    /**
     * Check if a payment method is allowed for this product
     */
    public function isPaymentMethodAllowed(string $method): bool
    {
        return in_array($method, $this->getAllowedPaymentMethods());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function hasVariants(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->where('is_active', true)->isNotEmpty();
        }

        return $this->activeVariants()->exists();
    }

    public function defaultVariant(): ?ProductVariant
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->where('is_active', true)->sortBy('sort_order')->first();
        }

        return $this->activeVariants()->first();
    }

    public function effectivePrice(): float
    {
        $variant = $this->defaultVariant();
        if ($variant) {
            return (float) $variant->price;
        }

        return (float) ($this->price ?? 0);
    }

    public function effectiveCompareAtPrice(): ?float
    {
        $variant = $this->defaultVariant();
        if ($variant) {
            return $variant->compare_at_price !== null ? (float) $variant->compare_at_price : null;
        }

        return $this->compare_at_price !== null ? (float) $this->compare_at_price : null;
    }

    public function effectiveStockQuantity(): int
    {
        if ($this->is_digital) {
            return PHP_INT_MAX;
        }

        if ($this->hasVariants()) {
            if ($this->relationLoaded('variants')) {
                return (int) $this->variants->where('is_active', true)->sum('stock_quantity');
            }
            return (int) $this->activeVariants()->sum('stock_quantity');
        }

        return (int) ($this->stock_quantity ?? 0);
    }

    public function effectiveInStock(): bool
    {
        if ($this->is_digital) {
            return true;
        }

        if ($this->hasVariants()) {
            if ($this->relationLoaded('variants')) {
                return $this->variants->where('is_active', true)->contains(fn ($variant) => $variant->in_stock && $variant->stock_quantity > 0);
            }
            return $this->activeVariants()
                ->where('in_stock', true)
                ->where('stock_quantity', '>', 0)
                ->exists();
        }

        return (bool) $this->in_stock && (int) ($this->stock_quantity ?? 0) > 0;
    }

    public function getMainImageAttribute()
    {
        $variant = $this->defaultVariant();
        if ($variant && !empty($variant->image)) {
            return $variant->image;
        }

        $images = $this->images ?? [];
        return !empty($images) ? $images[0] : null;
    }

    /**
     * Whether this digital product has a downloadable file.
     */
    public function hasDigitalFile(): bool
    {
        return $this->is_digital && $this->digital_content_type === 'file' && !empty($this->digital_file_path);
    }

    /**
     * Whether this digital product has link/text content.
     */
    public function hasDigitalLink(): bool
    {
        return $this->is_digital && $this->digital_content_type === 'link' && !empty(trim((string) $this->digital_link_text));
    }
}
