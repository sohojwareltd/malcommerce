<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('sponsor')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(30)->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load('sponsor');

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function update(Request $request, Withdrawal $withdrawal, WithdrawalService $withdrawalService)
    {
        $request->validate([
            'action' => 'required|string|in:approve,cancel,inquiry',
            'admin_note' => 'nullable|string',
            'inquiry_note' => 'nullable|string',
        ]);

        switch ($request->action) {
            case 'approve':
                $withdrawalService->approve($withdrawal, $request->admin_note);
                break;
            case 'cancel':
                $withdrawalService->cancel($withdrawal, $request->admin_note);
                break;
            case 'inquiry':
                $withdrawalService->markInquiry($withdrawal, $request->inquiry_note ?? '');
                break;
        }

        return redirect()
            ->route('admin.withdrawals.show', $withdrawal)
            ->with('success', 'Withdrawal updated successfully.');
    }
}


