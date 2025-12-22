<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'total_referrals' => $user->referrals()->count(),
            'total_orders' => $user->orders()->count(),
            'total_revenue' => $user->orders()->where('status', '!=', 'cancelled')->sum('total_price'),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
        ];
        
        $recentOrders = $user->orders()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $referrals = $user->referrals()->withCount('orders')->get();
        
        $affiliateLink = url('/') . '?ref=' . $user->affiliate_code;
        
        // Get all active products for affiliate links
        $products = Product::where('is_active', true)->orderBy('name')->get();
        
        return view('sponsor.dashboard', compact('stats', 'recentOrders', 'referrals', 'affiliateLink', 'products'));
    }
}
