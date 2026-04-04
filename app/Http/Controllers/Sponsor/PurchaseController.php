<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function storeOwn(Request $request)
    {
        $sponsor = Auth::user();

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'comment' => 'nullable|string|max:2000',
        ]);

        Purchase::create([
            'submitted_by_sponsor_id' => $sponsor->id,
            'beneficiary_user_id' => $sponsor->id,
            'kind' => Purchase::KIND_OWN,
            'amount' => $data['amount'],
            'comment' => $data['comment'] ?? null,
            'status' => Purchase::STATUS_PENDING,
        ]);

        return redirect()->back()->with('success', 'Purchase submitted. After approval, your balance will be credited with your commission on this amount (not the full purchase total).');
    }

    public function storeTeam(Request $request, User $referral)
    {
        $sponsor = Auth::user();

        if ($referral->sponsor_id !== $sponsor->id) {
            abort(403);
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'comment' => 'nullable|string|max:2000',
        ]);

        Purchase::create([
            'submitted_by_sponsor_id' => $sponsor->id,
            'beneficiary_user_id' => $referral->id,
            'kind' => Purchase::KIND_TEAM,
            'amount' => $data['amount'],
            'comment' => $data['comment'] ?? null,
            'status' => Purchase::STATUS_PENDING,
        ]);

        return redirect()->back()->with('success', 'Team purchase submitted for ' . $referral->name . '. If approved, their balance will be credited with commission on this amount only.');
    }
}
