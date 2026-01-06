<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EarningService
{
    /**
     * Generic earning creator.
     *
     * @param array $data
     * @return Earning
     */
    public function createEarning(array $data): Earning
    {
        return DB::transaction(function () use ($data) {
            /** @var User $sponsor */
            $sponsor = User::findOrFail($data['sponsor_id']);

            $platformPercentage = (float) Setting::get('platform_revenue_percentage', 0);
            $amount = (float) $data['amount'];

            $earning = Earning::create([
                'sponsor_id' => $sponsor->id,
                'referral_id' => $data['referral_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'earning_type' => $data['earning_type'],
                'comment' => $data['comment'] ?? null,
                'amount' => $amount,
                'platform_revenue' => round($amount * ($platformPercentage / 100), 2),
                'meta' => $data['meta'] ?? null,
            ]);

            // Increment sponsor balance
            $sponsor->increment('balance', $amount);

            return $earning;
        });
    }

    /**
     * Cashback earning for the customer or sponsor.
     */
    public function createCashbackEarning(Order $order, Product $product, User $customer, ?User $sponsorForCashback = null): ?Earning
    {
        if ((float) $product->cashback_amount <= 0) {
            return null;
        }

        $sponsor = $sponsorForCashback ?? $customer;

        return $this->createEarning([
            'sponsor_id' => $sponsor->id,
            'referral_id' => $customer->id,
            'order_id' => $order->id,
            'earning_type' => 'cashback',
            'amount' => $product->cashback_amount,
            'comment' => 'Cashback for order #' . $order->order_number,
            'meta' => [
                'product_id' => $product->id,
                'quantity' => $order->quantity,
            ],
        ]);
    }

    /**
     * Referral commission for the sponsor who referred the customer.
     */
    public function createReferralEarning(Order $order, Product $product, User $sponsor, User $customer): ?Earning
    {
        if (!$sponsor) {
            return null;
        }

        $commission = 0;
        $baseAmount = (float) $order->total_price;

        if ($product->commission_type === 'percent') {
            $commission = $baseAmount * ((float) $product->commission_value / 100);
        } else {
            $commission = (float) $product->commission_value;
        }

        // Nothing to record
        if ($commission <= 0) {
            return null;
        }

        return $this->createEarning([
            'sponsor_id' => $sponsor->id,
            'referral_id' => $customer->id,
            'order_id' => $order->id,
            'earning_type' => 'referral',
            'amount' => $commission,
            'comment' => 'Referral commission for order #' . $order->order_number,
            'meta' => [
                'product_id' => $product->id,
                'quantity' => $order->quantity,
            ],
        ]);
    }
}



