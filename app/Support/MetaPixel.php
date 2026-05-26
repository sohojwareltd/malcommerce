<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;

class MetaPixel
{
    public static function enabled(): bool
    {
        return (bool) Setting::get('fb_pixel_id');
    }

    /**
     * @return array{id: string, name: string, slug: string, category: string, price: float, currency: string}
     */
    public static function productPayload(Product $product): array
    {
        $product->loadMissing('category');

        return [
            'id' => (string) $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'category' => $product->category?->name ?? '',
            'price' => (float) $product->price,
            'currency' => 'BDT',
        ];
    }

    /**
     * @return array{order_id: string, value: float, currency: string, num_items: int, payment_method: string|null, product: array|null}
     */
    public static function orderPayload(Order $order): array
    {
        $order->loadMissing('product.category');
        $product = $order->product;

        return [
            'order_id' => $order->order_number,
            'value' => (float) $order->total_price,
            'currency' => 'BDT',
            'num_items' => (int) $order->quantity,
            'payment_method' => $order->payment_method,
            'product' => $product ? [
                'id' => (string) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => $product->category?->name ?? '',
                'price' => (float) $product->price,
            ] : null,
        ];
    }
}
