<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SponsorIncome;
use App\Models\SponsorLevel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
     * Total referral commission budget for an order (same basis as legacy single-sponsor payout).
     */
    public function referralCommissionBudget(Order $order, Product $product): float
    {
        $baseAmount = (float) $order->total_price;

        if ($product->commission_type === 'percent') {
            return round($baseAmount * ((float) $product->commission_value / 100), 2);
        }

        return round((float) $product->commission_value, 2);
    }

    /**
     * Referral commission for the sponsor who referred the customer (legacy: full budget to one sponsor).
     */
    public function createReferralEarning(Order $order, Product $product, User $sponsor, User $customer): ?Earning
    {
        if (! $sponsor) {
            return null;
        }

        $commission = $this->referralCommissionBudget($order, $product);

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

    /**
     * Referral payouts: level differential split when direct sponsor has a level; otherwise legacy single earning.
     *
     * @return list<Earning>
     */
    public function createReferralEarningsWithLevels(Order $order, Product $product, User $directSponsor, User $customer): array
    {
        $budget = $this->referralCommissionBudget($order, $product);
        if ($budget <= 0) {
            return [];
        }

        if (! $directSponsor->sponsor_level_id) {
            $one = $this->createReferralEarning($order, $product, $directSponsor, $customer);

            return $one ? [$one] : [];
        }

        if (! SponsorLevel::query()->exists()) {
            $one = $this->createReferralEarning($order, $product, $directSponsor, $customer);

            return $one ? [$one] : [];
        }

        $chain = $this->resolveSponsorUplineChain($directSponsor);
        if ($chain === []) {
            return [];
        }

        $weights = $this->levelDifferentialWeights($chain);
        $slices = [];
        foreach ($chain as $i => $sponsor) {
            $w = $weights[$i];
            if ($w > 0) {
                $slices[] = ['user' => $sponsor, 'w' => $w];
            }
        }

        if ($slices === []) {
            return [];
        }

        $totalW = array_sum(array_column($slices, 'w'));
        if ($totalW <= 0) {
            return [];
        }

        $earnings = [];
        $allocated = 0.0;
        $last = count($slices) - 1;

        foreach ($slices as $j => $slice) {
            $sponsor = $slice['user'];
            $w = $slice['w'];
            $amount = $j === $last
                ? round($budget - $allocated, 2)
                : round($budget * ($w / $totalW), 2);
            $allocated += $amount;

            if ($amount <= 0) {
                continue;
            }

            $earnings[] = $this->createEarning([
                'sponsor_id' => $sponsor->id,
                'referral_id' => $customer->id,
                'order_id' => $order->id,
                'earning_type' => 'referral',
                'amount' => $amount,
                'comment' => 'Referral commission (level split) for order #' . $order->order_number,
                'meta' => [
                    'product_id' => $product->id,
                    'quantity' => $order->quantity,
                    'level_differential' => true,
                    'delta_percent' => $w,
                    'weight_total_percent' => $totalW,
                ],
            ]);
        }

        return $earnings;
    }

    /**
     * @return list<User>
     */
    protected function resolveSponsorUplineChain(User $directSponsor): array
    {
        $orderedIds = [];
        $seen = [];
        $current = $directSponsor;

        for ($depth = 0; $depth < 50 && $current; $depth++) {
            if (isset($seen[$current->id])) {
                break;
            }
            $seen[$current->id] = true;
            $orderedIds[] = $current->id;
            $nextId = $current->sponsor_id;
            if (! $nextId) {
                break;
            }
            $current = User::query()->whereKey($nextId)->first();
        }

        if ($orderedIds === []) {
            return [];
        }

        $orderMap = array_flip($orderedIds);

        return User::query()
            ->whereIn('id', $orderedIds)
            ->with('sponsorLevel')
            ->get()
            ->sortBy(fn (User $u) => $orderMap[$u->id] ?? 0)
            ->values()
            ->all();
    }

    /**
     * Differential weight (percentage points) per chain node; same or invalid upline rank => 0.
     *
     * @param  list<User>  $chain
     * @return list<float>
     */
    protected function levelDifferentialWeights(array $chain): array
    {
        $maxRate = 0.0;
        $weights = [];

        foreach ($chain as $i => $user) {
            $level = $user->sponsorLevel;
            $rate = $level ? (float) $level->commission_percent : 0.0;
            $rank = $level ? (int) $level->rank : PHP_INT_MAX;

            if ($i > 0) {
                $prev = $chain[$i - 1];
                $prevLevel = $prev->sponsorLevel;
                $prevRank = $prevLevel ? (int) $prevLevel->rank : PHP_INT_MAX;
                if ($rank >= $prevRank) {
                    $weights[] = 0.0;

                    continue;
                }
            }

            $delta = max(0.0, $rate - $maxRate);
            $weights[] = $delta;
            if ($delta > 0) {
                $maxRate = max($maxRate, $rate);
            }
        }

        return $weights;
    }

    /**
     * Credit a user's balance from an admin-approved purchase (own or team).
     * Earning.sponsor_id stores the beneficiary (who receives the credit).
     * Balance increases by commission only: gross purchase amount × commission % (beneficiary's level, or setting fallback).
     */
    /**
     * Must be called inside DB::transaction when accepting a purchase (with row lock).
     */
    public function createPurchaseCreditEarning(
        \App\Models\Purchase $purchase,
        User $beneficiary,
        User $submittedBy,
    ): Earning {
        $beneficiary->loadMissing('sponsorLevel');

        $gross = (float) $purchase->amount;
        $commissionPercent = $beneficiary->sponsorLevel
            ? (float) $beneficiary->sponsorLevel->commission_percent
            : (float) Setting::get('purchase_approval_commission_percent', 0);

        $credit = round(max(0, $gross * ($commissionPercent / 100)), 2);
        $platformPercentage = (float) Setting::get('platform_revenue_percentage', 0);

        $earning = Earning::create([
            'sponsor_id' => $beneficiary->id,
            'referral_id' => $purchase->kind === \App\Models\Purchase::KIND_TEAM ? $submittedBy->id : null,
            'order_id' => null,
            'earning_type' => 'purchase',
            'comment' => $purchase->comment
                ? 'Purchase commission: ' . $purchase->comment
                : 'Purchase commission (approved)',
            'amount' => $credit,
            'platform_revenue' => round($credit * ($platformPercentage / 100), 2),
            'meta' => [
                'purchase_id' => $purchase->id,
                'kind' => $purchase->kind,
                'purchase_gross_amount' => $gross,
                'commission_percent' => $commissionPercent,
                'commission_amount' => $credit,
            ],
        ]);

        if ($credit > 0) {
            $beneficiary->increment('balance', $credit);
        }

        return $earning;
    }

    /**
     * Admin-added income: row in sponsor_incomes, matching earning, balance credit.
     */
    public function createManualSponsorIncome(
        User $sponsor,
        float $amount,
        string $category,
        ?string $notes,
        ?int $createdByUserId,
    ): SponsorIncome {
        $amount = round(max(0, $amount), 2);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($sponsor, $amount, $category, $notes, $createdByUserId) {
            $income = SponsorIncome::create([
                'sponsor_id' => $sponsor->id,
                'amount' => $amount,
                'category' => $category,
                'notes' => $notes,
                'created_by' => $createdByUserId,
            ]);

            $platformPercentage = (float) Setting::get('platform_revenue_percentage', 0);
            $comment = Str::limit(trim($category.($notes ? ' — '.$notes : '')), 250);

            $earning = Earning::create([
                'sponsor_id' => $sponsor->id,
                'referral_id' => null,
                'order_id' => null,
                'earning_type' => 'manual_income',
                'comment' => $comment !== '' ? $comment : $category,
                'amount' => $amount,
                'platform_revenue' => round($amount * ($platformPercentage / 100), 2),
                'meta' => [
                    'sponsor_income_id' => $income->id,
                    'category' => $category,
                    'notes' => $notes,
                ],
            ]);

            $sponsor->increment('balance', $amount);
            $income->update(['earning_id' => $earning->id]);

            return $income->fresh(['earning', 'creator']);
        });
    }
}

