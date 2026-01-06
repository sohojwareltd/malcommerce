<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->earnings()->with(['order', 'referral'])->latest();

        if ($request->filled('type')) {
            $query->where('earning_type', $request->type);
        }

        $earnings = $query->paginate(20)->withQueryString();

        $stats = [
            'total_earnings' => (float) $user->earnings()->sum('amount'),
        ];

        return view('sponsor.earnings.index', compact('earnings', 'stats', 'user'));
    }
}



