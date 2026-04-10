<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Purchase;
use App\Models\User;
use App\Services\EarningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        if (! in_array($status, ['pending', 'accepted', 'canceled', 'all'], true)) {
            $status = 'pending';
        }

        $query = Purchase::query()
            ->with(['submittedBy', 'beneficiary', 'processedBy'])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $purchases = $query->paginate(30)->withQueryString();

        $counts = [
            'pending' => Purchase::where('status', Purchase::STATUS_PENDING)->count(),
            'accepted' => Purchase::where('status', Purchase::STATUS_ACCEPTED)->count(),
            'canceled' => Purchase::where('status', Purchase::STATUS_CANCELED)->count(),
        ];

        return view('admin.purchases.index', compact('purchases', 'status', 'counts'));
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

    public function updateStatus(Request $request, Purchase $purchase, EarningService $earningService)
    {
        $data = $request->validate([
            'status' => 'required|in:accepted,canceled',
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

        $accepted = false;
        DB::transaction(function () use ($purchase, $earningService, &$accepted) {
            $locked = Purchase::with(['beneficiary.sponsorLevel', 'submittedBy'])
                ->whereKey($purchase->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $locked->isPending()) {
                return;
            }

            $earning = $earningService->createPurchaseCreditEarning(
                $locked,
                $locked->beneficiary,
                $locked->submittedBy
            );

            $locked->update([
                'status' => Purchase::STATUS_ACCEPTED,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'earning_id' => $earning->id,
            ]);

            $accepted = true;
        });

        if (! $accepted) {
            return redirect()->back()->with('error', 'This purchase was already processed.');
        }

        return redirect()->back()->with('success', 'Purchase accepted. Beneficiary balance increased by commission only (see linked earning).');
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
}
