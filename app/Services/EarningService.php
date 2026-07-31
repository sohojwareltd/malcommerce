<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\SponsorIncome;
use App\Models\User;
use Illuminate\Support\Collection;
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
        // Order/referral income should not change with sponsor level updates.
        // Keep legacy behavior: full referral commission goes to direct sponsor only.
        $one = $this->createReferralEarning($order, $product, $directSponsor, $customer);

        return $one ? [$one] : [];
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
     * Chain order: index 0 = direct sponsor (deepest toward the customer), then upline toward the root.
     * Rank convention: lower number = higher in the tree (0 top, then 1, 2, … e.g. 6 deepest).
     *
     * @param  list<User>  $chain
     * @param  list<float>|null  $rates  Optional per-node rates (same length as $chain); defaults to each user’s level commission %.
     * @return list<float>
     */
    protected function levelDifferentialWeights(array $chain, ?array $rates = null): array
    {
        $maxRate = 0.0;
        $weights = [];

        foreach ($chain as $i => $user) {
            $level = $user->sponsorLevel;
            $rate = $rates[$i] ?? ($level ? (float) $level->commission_percent : 0.0);
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
     * Level rates + differential weights for a purchase beneficiary chain.
     *
     * @param  list<User>  $chain
     * @return array{0: list<float>, 1: list<float>} [levelRates, differentialWeights]
     */
    protected function purchaseCommissionDifferentials(array $chain, float $defaultCommissionPercent): array
    {
        $levelRates = [];
        foreach ($chain as $recipient) {
            $recipient->loadMissing('sponsorLevel');
            $levelRates[] = $this->purchaseCommissionPercentForUser($recipient, $defaultCommissionPercent);
        }

        return [$levelRates, $this->levelDifferentialWeights($chain, $levelRates)];
    }

    /**
     * Credit balances from an admin-approved purchase (own or team).
     * Pays the beneficiary and every referrer up the chain (sponsor_id) until there is no referrer.
     * Each recipient gets gross × their level-differential weight % (gaps between rates up the upline;
     * level rate from sponsor level or purchase_approval_commission_percent fallback).
     * Earning linked on the purchase row is the beneficiary’s record (may be zero if their weight is 0).
     *
     * Must be called inside DB::transaction when accepting a purchase (with row lock).
     */
    public function createPurchaseCreditEarning(
        \App\Models\Purchase $purchase,
        User $beneficiary,
        User $submittedBy,
    ): Earning {
        $gross = (float) $purchase->amount;
        $defaultCommissionPercent = (float) Setting::get('purchase_approval_commission_percent', 0);
        $platformPercentage = (float) Setting::get('platform_revenue_percentage', 0);

        $chain = $this->resolveSponsorUplineChain($beneficiary);
        if ($chain === []) {
            $beneficiary->loadMissing('sponsorLevel');
            $chain = [$beneficiary];
        }

        [$levelRates, $weights] = $this->purchaseCommissionDifferentials($chain, $defaultCommissionPercent);

        $primaryEarning = null;
        $baseComment = $purchase->comment
            ? 'Purchase commission: '.$purchase->comment
            : 'Purchase commission (approved)';

        foreach ($chain as $index => $recipient) {
            $levelPercent = $levelRates[$index];
            $commissionPercent = $weights[$index];
            $credit = round(max(0, $gross * ($commissionPercent / 100)), 2);
            $isBeneficiary = $recipient->id === $beneficiary->id;

            if (! $isBeneficiary && $credit <= 0) {
                continue;
            }

            $referralId = null;
            if ($purchase->kind === \App\Models\Purchase::KIND_TEAM) {
                $referralId = $isBeneficiary ? $submittedBy->id : $beneficiary->id;
            } elseif (! $isBeneficiary) {
                $referralId = $beneficiary->id;
            }

            $comment = $baseComment;
            if (! $isBeneficiary) {
                $comment .= ' (upline)';
            }

            $earning = Earning::create([
                'sponsor_id' => $recipient->id,
                'referral_id' => $referralId,
                'order_id' => null,
                'earning_type' => 'purchase',
                'comment' => $comment,
                'amount' => $credit,
                'platform_revenue' => round($credit * ($platformPercentage / 100), 2),
                'meta' => [
                    'purchase_id' => $purchase->id,
                    'kind' => $purchase->kind,
                    'purchase_gross_amount' => $gross,
                    'level_commission_percent' => $levelPercent,
                    'commission_percent' => $commissionPercent,
                    'commission_amount' => $credit,
                    'purchase_chain_index' => $index,
                    'purchase_beneficiary_id' => $beneficiary->id,
                    'is_purchase_beneficiary' => $isBeneficiary,
                ],
            ]);

            if ($isBeneficiary) {
                $primaryEarning = $earning;
            }

            if ($credit > 0) {
                $recipient->increment('balance', $credit);
            }
        }

        if (! $primaryEarning) {
            throw new \RuntimeException('Purchase commission: could not create beneficiary earning.');
        }

        return $primaryEarning;
    }

    /**
     * Estimated wallet credit from pending purchases for this sponsor if all were approved with current rates
     * (mirror of {@see createPurchaseCreditEarning} math; excludes balance mutations).
     *
     * @param  Collection<int, Purchase>|null  $prefetchedPurchases  Pending purchases (with beneficiary), or load all pending.
     */
    public function estimatePendingPurchaseCommissionForSponsor(User $sponsor, ?Collection $prefetchedPurchases = null): float
    {
        $purchases = $prefetchedPurchases ?? Purchase::query()
            ->where('status', Purchase::STATUS_PENDING)
            ->with(['beneficiary', 'submittedBy'])
            ->get();

        $defaultCommissionPercent = (float) Setting::get('purchase_approval_commission_percent', 0);
        $chainByBeneficiaryId = [];
        $weightsByBeneficiaryId = [];
        $total = 0.0;

        foreach ($purchases as $purchase) {
            $beneficiary = $purchase->beneficiary;
            if (! $beneficiary) {
                continue;
            }

            $bid = $beneficiary->id;
            if (! isset($chainByBeneficiaryId[$bid])) {
                $chain = $this->resolveSponsorUplineChain($beneficiary);
                if ($chain === []) {
                    $beneficiary->loadMissing('sponsorLevel');
                    $chain = [$beneficiary];
                }
                $chainByBeneficiaryId[$bid] = $chain;
                [, $weightsByBeneficiaryId[$bid]] = $this->purchaseCommissionDifferentials($chain, $defaultCommissionPercent);
            } else {
                $chain = $chainByBeneficiaryId[$bid];
            }

            $weights = $weightsByBeneficiaryId[$bid];
            $gross = (float) $purchase->amount;

            foreach ($chain as $index => $recipient) {
                if ($recipient->id !== $sponsor->id) {
                    continue;
                }

                $commissionPercent = $weights[$index];
                $credit = round(max(0, $gross * ($commissionPercent / 100)), 2);
                $isBeneficiary = $recipient->id === $beneficiary->id;

                if (! $isBeneficiary && $credit <= 0) {
                    break;
                }

                $total += $credit;
                break;
            }
        }

        return round($total, 2);
    }

    /**
     * Sum estimated commission per user from a set of pending purchases (one pass; mirrors {@see createPurchaseCreditEarning}).
     * Used for admin rankings; only includes users who appear on an upline chain for at least one purchase.
     *
     * @param  Collection<int, Purchase>  $purchases
     * @return Collection<int, float> user id => total estimated commission
     */
    public function aggregatePendingPurchaseCommissionBySponsor(Collection $purchases): Collection
    {
        $defaultCommissionPercent = (float) Setting::get('purchase_approval_commission_percent', 0);
        $chainByBeneficiaryId = [];
        $weightsByBeneficiaryId = [];
        $totals = [];

        foreach ($purchases as $purchase) {
            $beneficiary = $purchase->beneficiary;
            if (! $beneficiary) {
                continue;
            }

            $bid = $beneficiary->id;
            if (! isset($chainByBeneficiaryId[$bid])) {
                $chain = $this->resolveSponsorUplineChain($beneficiary);
                if ($chain === []) {
                    $beneficiary->loadMissing('sponsorLevel');
                    $chain = [$beneficiary];
                }
                $chainByBeneficiaryId[$bid] = $chain;
                [, $weightsByBeneficiaryId[$bid]] = $this->purchaseCommissionDifferentials($chain, $defaultCommissionPercent);
            } else {
                $chain = $chainByBeneficiaryId[$bid];
            }

            $weights = $weightsByBeneficiaryId[$bid];
            $gross = (float) $purchase->amount;

            foreach ($chain as $index => $recipient) {
                $commissionPercent = $weights[$index];
                $credit = round(max(0, $gross * ($commissionPercent / 100)), 2);
                $isBeneficiary = (int) $recipient->id === (int) $beneficiary->id;

                if (! $isBeneficiary && $credit <= 0) {
                    continue;
                }

                $sid = (int) $recipient->id;
                $totals[$sid] = round(($totals[$sid] ?? 0) + $credit, 2);
            }
        }

        return collect($totals);
    }

    /**
     * Purchase commission % for a user: level rate when assigned; 0% level (any rank) pays nothing.
     * Setting fallback only when the user has no sponsor level.
     */
    protected function purchaseCommissionPercentForUser(User $user, float $defaultPercent): float
    {
        $level = $user->sponsorLevel;
        if (! $level) {
            return $defaultPercent;
        }

        return max(0.0, (float) $level->commission_percent);
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

