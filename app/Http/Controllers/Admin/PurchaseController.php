<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\EarningService;
use App\Services\WithdrawalService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $status = $this->normalizePurchaseStatus($request->get('status', 'pending'));
        $periodType = $this->normalizePurchasePeriodType($request->input('period', 'all'));

        $query = $this->purchasesFilteredQuery($request, $status, $periodType);

        $purchases = $query->paginate(30)->withQueryString();

        $counts = [
            'pending' => Purchase::where('status', Purchase::STATUS_PENDING)->count(),
            'accepted' => Purchase::where('status', Purchase::STATUS_ACCEPTED)->count(),
            'canceled' => Purchase::where('status', Purchase::STATUS_CANCELED)->count(),
        ];

        $rangeLabel = $this->purchasesPeriodLabel($request, $periodType);

        return view('admin.purchases.index', compact(
            'purchases',
            'status',
            'counts',
            'periodType',
            'rangeLabel'
        ));
    }

    public function printReport(Request $request)
    {
        $status = $this->normalizePurchaseStatus($request->get('status', 'all'));
        $periodType = $this->normalizePurchasePeriodType($request->input('period', 'all'));

        $purchases = $this->purchasesFilteredQuery($request, $status, $periodType)->get();

        $rangeLabel = $this->purchasesPeriodLabel($request, $periodType);
        $statusLabel = $status === 'all' ? 'All statuses' : ucfirst($status);

        $printSummary = [
            'purchase_count' => $purchases->count(),
            'total_amount' => (float) $purchases->sum(fn (Purchase $p) => (float) $p->amount),
        ];

        if ($status === 'all') {
            $printSummary['pending_count'] = $purchases->where('status', Purchase::STATUS_PENDING)->count();
            $printSummary['accepted_count'] = $purchases->where('status', Purchase::STATUS_ACCEPTED)->count();
            $printSummary['canceled_count'] = $purchases->where('status', Purchase::STATUS_CANCELED)->count();
            $printSummary['pending_amount'] = (float) $purchases->where('status', Purchase::STATUS_PENDING)->sum(fn (Purchase $p) => (float) $p->amount);
            $printSummary['accepted_amount'] = (float) $purchases->where('status', Purchase::STATUS_ACCEPTED)->sum(fn (Purchase $p) => (float) $p->amount);
            $printSummary['canceled_amount'] = (float) $purchases->where('status', Purchase::STATUS_CANCELED)->sum(fn (Purchase $p) => (float) $p->amount);
        }

        return view('admin.purchases.print.report', compact(
            'purchases',
            'status',
            'periodType',
            'rangeLabel',
            'statusLabel',
            'printSummary'
        ));
    }

    protected function normalizePurchaseStatus(string $status): string
    {
        if (! in_array($status, ['pending', 'accepted', 'canceled', 'all'], true)) {
            return 'pending';
        }

        return $status;
    }

    protected function normalizePurchasePeriodType(?string $period): string
    {
        $period = $period ?? 'all';
        if (! in_array($period, ['all', 'month', 'range'], true)) {
            return 'all';
        }

        return $period;
    }

    protected function purchasesFilteredQuery(Request $request, string $status, string $periodType): Builder
    {
        $query = Purchase::query()
            ->with(['submittedBy', 'beneficiary', 'processedBy'])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $this->applyPurchaseDateScope($query, $request, $periodType);

        return $query;
    }

    protected function applyPurchaseDateScope(Builder $query, Request $request, string $periodType): void
    {
        if ($periodType === 'month' && $request->filled('month') && preg_match('/^\d{4}-\d{2}$/', (string) $request->input('month'))) {
            $start = Carbon::createFromFormat('Y-m', $request->input('month'))->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $query->whereBetween('created_at', [$start, $end]);

            return;
        }

        if ($periodType === 'range') {
            $from = $request->filled('date_from')
                ? Carbon::parse($request->input('date_from'))->startOfDay()
                : null;
            $to = $request->filled('date_to')
                ? Carbon::parse($request->input('date_to'))->endOfDay()
                : null;
            if ($from && $to) {
                if ($to->lt($from)) {
                    [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
                }
                $query->whereBetween('created_at', [$from, $to]);
            } elseif ($from) {
                $query->where('created_at', '>=', $from);
            } elseif ($to) {
                $query->where('created_at', '<=', $to);
            }
        }
    }

    protected function purchasesPeriodLabel(Request $request, string $periodType): string
    {
        if ($periodType === 'month' && $request->filled('month') && preg_match('/^\d{4}-\d{2}$/', (string) $request->input('month'))) {
            return Carbon::createFromFormat('Y-m', $request->input('month'))->format('F Y');
        }

        if ($periodType === 'range') {
            $from = $request->filled('date_from')
                ? Carbon::parse($request->input('date_from'))->format('M j, Y')
                : null;
            $to = $request->filled('date_to')
                ? Carbon::parse($request->input('date_to'))->format('M j, Y')
                : null;
            if ($from && $to) {
                return $from.' – '.$to;
            }
            if ($from) {
                return 'From '.$from;
            }
            if ($to) {
                return 'Through '.$to;
            }
        }

        return 'All dates';
    }

    public function create()
    {
        $referrerOptions = User::query()
            ->where('role', 'sponsor')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'affiliate_code', 'phone']);

        return view('admin.purchases.create', compact('referrerOptions'));
    }

    public function store(Request $request, EarningService $earningService, WithdrawalService $withdrawalService)
    {
        $data = $request->validate([
            'submitted_by_sponsor_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'sponsor'))],
            'beneficiary_user_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'sponsor'))],
            'kind' => ['required', Rule::in([Purchase::KIND_OWN, Purchase::KIND_TEAM])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'approve_immediately' => ['nullable', 'boolean'],
            'withdraw_after_accept' => ['nullable', 'boolean'],
        ]);

        $approveNow = $request->boolean('approve_immediately');
        $withdrawAfter = $request->boolean('withdraw_after_accept');

        if ($withdrawAfter && ! $approveNow) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['withdraw_after_accept' => 'Withdraw after approval requires “Approve immediately” to be checked.']);
        }

        if ($withdrawAfter) {
            $this->authorize('create', Withdrawal::class);
        }

        $submitter = User::query()->whereKey((int) $data['submitted_by_sponsor_id'])->firstOrFail();
        $beneficiary = User::query()->whereKey((int) $data['beneficiary_user_id'])->firstOrFail();

        if ($data['kind'] === Purchase::KIND_OWN && (int) $submitter->id !== (int) $beneficiary->id) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['beneficiary_user_id' => 'For an own purchase, beneficiary must be the same partner as submitted by.']);
        }

        if ($data['kind'] === Purchase::KIND_TEAM && (int) $beneficiary->sponsor_id !== (int) $submitter->id) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['beneficiary_user_id' => 'For a team purchase, beneficiary must be a direct referral of the submitting partner.']);
        }

        return DB::transaction(function () use ($request, $data, $approveNow, $withdrawAfter, $earningService, $withdrawalService, $submitter, $beneficiary) {
            $purchase = Purchase::create([
                'submitted_by_sponsor_id' => $submitter->id,
                'beneficiary_user_id' => $beneficiary->id,
                'kind' => $data['kind'],
                'amount' => $data['amount'],
                'comment' => $data['comment'] ?? null,
                'status' => Purchase::STATUS_PENDING,
            ]);

            if (! $approveNow) {
                return redirect()
                    ->route('admin.purchases.show', ['purchase' => $purchase, 'from_status' => 'pending'])
                    ->with('success', 'Purchase request recorded (pending review).');
            }

            $locked = Purchase::with(['beneficiary.sponsorLevel', 'submittedBy'])
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            $earning = $this->acceptPendingPurchase($locked, $earningService, Auth::id());

            if ($withdrawAfter) {
                $this->withdrawBeneficiaryCommissionCash($withdrawalService, $locked, $earning);
            }

            return redirect()
                ->route('admin.purchases.show', ['purchase' => $locked->fresh(['earning']), 'from_status' => 'accepted'])
                ->with(
                    'success',
                    $withdrawAfter
                        ? 'Purchase approved; beneficiary commission withdrawn as cash payout.'
                        : 'Purchase approved and commissions credited.'
                );
        });
    }

    public function show(Request $request, Purchase $purchase)
    {
        $backStatus = $request->query('from_status', 'pending');
        if (! in_array($backStatus, ['pending', 'accepted', 'canceled', 'all'], true)) {
            $backStatus = 'pending';
        }

        $purchase->load(['submittedBy', 'beneficiary', 'processedBy', 'earning']);

        return view('admin.purchases.show', compact('purchase', 'backStatus'));
    }

    public function updateStatus(Request $request, Purchase $purchase, EarningService $earningService, WithdrawalService $withdrawalService)
    {
        $data = $request->validate([
            'status' => ['required', 'in:accepted,canceled'],
            'withdraw_after_accept' => ['nullable', 'boolean'],
        ]);

        if (! $purchase->isPending()) {
            return redirect()->back()->with('error', 'This purchase is no longer pending.');
        }

        if ($data['status'] === 'canceled') {
            $canceled = false;
            DB::transaction(function () use ($purchase, &$canceled) {
                $locked = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();
                if (! $locked->isPending()) {
                    return;
                }
                $locked->update([
                    'status' => Purchase::STATUS_CANCELED,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
                $canceled = true;
            });

            if (! $canceled) {
                return redirect()->back()->with('error', 'This purchase was already processed.');
            }

            return redirect()->back()->with('success', 'Purchase canceled.');
        }

        $withdrawAfter = $request->boolean('withdraw_after_accept');
        if ($withdrawAfter) {
            $this->authorize('create', Withdrawal::class);
        }

        $accepted = false;
        DB::transaction(function () use ($purchase, $earningService, $withdrawalService, $withdrawAfter, &$accepted) {
            $locked = Purchase::with(['beneficiary.sponsorLevel', 'submittedBy'])
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $locked->isPending()) {
                return;
            }

            $earning = $this->acceptPendingPurchase($locked, $earningService, Auth::id());

            if ($withdrawAfter) {
                $this->withdrawBeneficiaryCommissionCash($withdrawalService, $locked, $earning);
            }

            $accepted = true;
        });

        if (! $accepted) {
            return redirect()->back()->with('error', 'This purchase was already processed.');
        }

        return redirect()->back()->with(
            'success',
            $withdrawAfter
                ? 'Purchase accepted; beneficiary commission recorded as a cash withdrawal request.'
                : 'Purchase accepted. Beneficiary balance increased by commission only (see linked earning).'
        );
    }

    public function destroy(Request $request, Purchase $purchase)
    {
        $statusFrom = $request->input('from_status', 'all');
        if (! in_array($statusFrom, ['pending', 'accepted', 'canceled', 'all'], true)) {
            $statusFrom = 'all';
        }

        $rolledBack = false;
        $rolledBackCount = 0;

        DB::transaction(function () use ($purchase, &$rolledBack, &$rolledBackCount) {
            $locked = Purchase::query()
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status === Purchase::STATUS_ACCEPTED) {
                $earnings = Earning::query()
                    ->where('earning_type', 'purchase')
                    ->where(function ($q) use ($locked) {
                        if ($locked->earning_id) {
                            $q->whereKey($locked->earning_id);
                        }
                        $q->orWhere('meta->purchase_id', $locked->id);
                    })
                    ->lockForUpdate()
                    ->get()
                    ->unique('id')
                    ->values();

                foreach ($earnings as $earning) {
                    $recipient = User::query()->whereKey($earning->sponsor_id)->lockForUpdate()->first();
                    if ($recipient) {
                        $recipient->decrement('balance', (float) $earning->amount);
                    }
                    $earning->delete();
                    $rolledBackCount++;
                }

                $rolledBack = $rolledBackCount > 0;
            }

            $locked->delete();
        });

        if ($rolledBack) {
            return redirect()
                ->route('admin.purchases.index', ['status' => $statusFrom])
                ->with('success', "Purchase deleted. Rolled back {$rolledBackCount} commission earning(s) and sponsor balance credit(s).");
        }

        return redirect()
            ->route('admin.purchases.index', ['status' => $statusFrom])
            ->with('success', 'Purchase request deleted.');
    }

    /**
     * Accept a locked pending purchase; updates row and returns the primary (beneficiary) earning.
     */
    protected function acceptPendingPurchase(Purchase $locked, EarningService $earningService, ?int $processedByUserId): Earning
    {
        $earning = $earningService->createPurchaseCreditEarning(
            $locked,
            $locked->beneficiary,
            $locked->submittedBy
        );

        $locked->update([
            'status' => Purchase::STATUS_ACCEPTED,
            'processed_by' => $processedByUserId,
            'processed_at' => now(),
            'earning_id' => $earning->id,
        ]);

        return $earning;
    }

    /**
     * Withdraw the beneficiary’s credited commission as an admin cash payout (same balance rules as manual cash withdrawal).
     */
    protected function withdrawBeneficiaryCommissionCash(WithdrawalService $withdrawalService, Purchase $purchase, Earning $beneficiaryEarning): void
    {
        $credit = round((float) $beneficiaryEarning->amount, 2);
        if ($credit <= 0) {
            return;
        }

        $ben = User::query()->whereKey($purchase->beneficiary_user_id)->lockForUpdate()->firstOrFail();
        $ben->refresh();

        $withdrawalService->requestWithdrawal(
            $ben,
            $credit,
            [
                'provider' => 'cash',
                'method_key' => 'cash_purchase_'.Str::lower(Str::random(10)),
                'label' => 'Cash (after purchase #'.$purchase->id.')',
                'number' => '',
                'account_type' => 'personal',
                'holder_name' => $ben->name,
                'admin_pickup_note' => 'Auto: purchase #'.$purchase->id,
            ]
        );
    }
}
