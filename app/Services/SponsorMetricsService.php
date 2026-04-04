<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SponsorMetricsService
{
    public const DEFAULT_MONTHS = 12;

    /**
     * @return array{months: list<string>, amounts: list<float>, counts: list<int>}
     */
    public function acceptedPurchaseMonthlySeries(int $sponsorId, int $months = self::DEFAULT_MONTHS): array
    {
        $keys = $this->monthKeys($months);
        $amounts = array_fill_keys($keys, 0.0);
        $counts = array_fill_keys($keys, 0);

        $rows = Purchase::query()
            ->where('submitted_by_sponsor_id', $sponsorId)
            ->where('status', Purchase::STATUS_ACCEPTED)
            ->whereNotNull('processed_at')
            ->where('processed_at', '>=', $this->windowStart($months))
            ->selectRaw('DATE_FORMAT(processed_at, "%Y-%m") as ym, SUM(amount) as total, COUNT(*) as c')
            ->groupBy('ym')
            ->get();

        foreach ($rows as $row) {
            $ym = $row->ym;
            if (isset($amounts[$ym])) {
                $amounts[$ym] = (float) $row->total;
                $counts[$ym] = (int) $row->c;
            }
        }

        return [
            'months' => $keys,
            'amounts' => array_values($amounts),
            'counts' => array_values($counts),
        ];
    }

    /**
     * @return array{months: list<string>, amounts: list<float>, counts: list<int>}
     */
    public function withdrawalMonthlySeries(int $sponsorId, int $months = self::DEFAULT_MONTHS): array
    {
        $keys = $this->monthKeys($months);
        $amounts = array_fill_keys($keys, 0.0);
        $counts = array_fill_keys($keys, 0);

        $rows = Withdrawal::query()
            ->where('sponsor_id', $sponsorId)
            ->where('status', Withdrawal::STATUS_APPROVED)
            ->whereNotNull('processed_at')
            ->where('processed_at', '>=', $this->windowStart($months))
            ->selectRaw('DATE_FORMAT(processed_at, "%Y-%m") as ym, SUM(amount) as total, COUNT(*) as c')
            ->groupBy('ym')
            ->get();

        foreach ($rows as $row) {
            $ym = $row->ym;
            if (isset($amounts[$ym])) {
                $amounts[$ym] = (float) $row->total;
                $counts[$ym] = (int) $row->c;
            }
        }

        return [
            'months' => $keys,
            'amounts' => array_values($amounts),
            'counts' => array_values($counts),
        ];
    }

    /**
     * Consistency score (0–100) from month-to-month relative volatility of accepted purchase amounts.
     * Requires at least two calendar months in the window with processed_at data; otherwise null.
     */
    public function consistencyScoreFromAmounts(array $monthlyAmounts, int $monthsInWindow = self::DEFAULT_MONTHS): ?float
    {
        $values = array_values($monthlyAmounts);
        if (count($values) !== $monthsInWindow) {
            return null;
        }

        $activeMonths = count(array_filter($values, fn (float $v) => $v > 0));
        if ($activeMonths < 2) {
            return null;
        }

        $sum = array_sum($values);
        if ($sum <= 0) {
            return null;
        }

        $n = count($values);
        $mean = $sum / $n;
        $variance = 0.0;
        foreach ($values as $x) {
            $variance += ($x - $mean) ** 2;
        }
        $variance /= max(1, $n - 1);
        $std = sqrt($variance);
        $cv = $mean > 0 ? $std / $mean : 1.0;
        $score = 100 * max(0, 1 - min(1, $cv));

        return round($score, 1);
    }

    public function consistencyScore(int $sponsorId, int $months = self::DEFAULT_MONTHS): ?float
    {
        $series = $this->acceptedPurchaseMonthlySeries($sponsorId, $months);

        return $this->consistencyScoreFromAmounts($series['amounts'], $months);
    }

    /**
     * @return array{
     *   consistency: float|null,
     *   purchases: array,
     *   withdrawals: array,
     *   purchase_totals: array{day_30: float, month_current: float},
     *   withdrawal_totals: array{day_30: float, month_current: float},
     *   peer_sample_avg_consistency: float|null,
     *   referrer_consistency: float|null
     * }
     */
    public function dashboardMetrics(User $sponsor, int $months = self::DEFAULT_MONTHS): array
    {
        $purchases = $this->acceptedPurchaseMonthlySeries($sponsor->id, $months);
        $withdrawals = $this->withdrawalMonthlySeries($sponsor->id, $months);
        $consistency = $this->consistencyScoreFromAmounts($purchases['amounts'], $months);

        $start30 = now()->subDays(30)->startOfDay();
        $purchase30 = (float) Purchase::query()
            ->where('submitted_by_sponsor_id', $sponsor->id)
            ->where('status', Purchase::STATUS_ACCEPTED)
            ->where('processed_at', '>=', $start30)
            ->sum('amount');

        $monthStart = now()->startOfMonth();
        $purchaseMonth = (float) Purchase::query()
            ->where('submitted_by_sponsor_id', $sponsor->id)
            ->where('status', Purchase::STATUS_ACCEPTED)
            ->where('processed_at', '>=', $monthStart)
            ->sum('amount');

        $withdraw30 = (float) Withdrawal::query()
            ->where('sponsor_id', $sponsor->id)
            ->where('status', Withdrawal::STATUS_APPROVED)
            ->where('processed_at', '>=', $start30)
            ->sum('amount');

        $withdrawMonth = (float) Withdrawal::query()
            ->where('sponsor_id', $sponsor->id)
            ->where('status', Withdrawal::STATUS_APPROVED)
            ->where('processed_at', '>=', $monthStart)
            ->sum('amount');

        $referrerConsistency = null;
        if ($sponsor->sponsor_id) {
            $referrerConsistency = $this->consistencyScore((int) $sponsor->sponsor_id, $months);
        }

        $peerAvg = $this->peerSampleAverageConsistency($sponsor->id, $months);

        return [
            'consistency' => $consistency,
            'purchases' => $purchases,
            'withdrawals' => $withdrawals,
            'purchase_totals' => [
                'day_30' => $purchase30,
                'month_current' => $purchaseMonth,
            ],
            'withdrawal_totals' => [
                'day_30' => $withdraw30,
                'month_current' => $withdrawMonth,
            ],
            'peer_sample_avg_consistency' => $peerAvg,
            'referrer_consistency' => $referrerConsistency,
        ];
    }

    protected function peerSampleAverageConsistency(int $excludeSponsorId, int $months): ?float
    {
        return Cache::remember(
            "sponsor_metrics_peer_avg_consistency_{$excludeSponsorId}_{$months}_v1",
            3600,
            function () use ($excludeSponsorId, $months) {
                $ids = User::query()
                    ->where('role', 'sponsor')
                    ->where('id', '!=', $excludeSponsorId)
                    ->inRandomOrder()
                    ->limit(40)
                    ->pluck('id');

                if ($ids->isEmpty()) {
                    return null;
                }

                $scores = [];
                foreach ($ids as $id) {
                    $s = $this->consistencyScore((int) $id, $months);
                    if ($s !== null) {
                        $scores[] = $s;
                    }
                }

                if ($scores === []) {
                    return null;
                }

                return round(array_sum($scores) / count($scores), 1);
            }
        );
    }

    /**
     * @return list<string> Y-m oldest first
     */
    protected function monthKeys(int $months): array
    {
        $keys = [];
        $d = now()->startOfMonth()->subMonths($months - 1);
        for ($i = 0; $i < $months; $i++) {
            $keys[] = $d->format('Y-m');
            $d = $d->copy()->addMonth();
        }

        return $keys;
    }

    protected function windowStart(int $months): Carbon
    {
        return now()->startOfMonth()->subMonths($months - 1);
    }
}
