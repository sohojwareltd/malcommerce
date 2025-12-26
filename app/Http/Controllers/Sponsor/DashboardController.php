<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
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
    
    /**
     * Show the add user page
     */
    public function createUser()
    {
        $user = Auth::user();
        $referrals = $user->referrals()
            ->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('sponsor.users.create', compact('user', 'referrals'));
    }
    
    /**
     * Add a new user (referred by the current sponsor)
     */
    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
        ]);
        
        $sponsor = Auth::user();
        $phone = $this->normalizePhone($request->phone);
        
        // Check if user already exists
        if (User::where('phone', $phone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered.'
            ], 422);
        }
        
        // Create user with current sponsor as referrer
        $user = User::create([
            'name' => $request->name,
            'phone' => $phone,
            'role' => 'sponsor', // All users are sponsors
            'sponsor_id' => $sponsor->id, // Referred by current sponsor
            'password' => null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User added successfully!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'affiliate_code' => $user->affiliate_code,
            ]
        ]);
    }
    
    /**
     * Normalize phone number
     */
    protected function normalizePhone($phone)
    {
        // Remove spaces, dashes, and other characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it starts with 0, replace with country code
        if (strpos($phone, '0') === 0) {
            $phone = '880' . substr($phone, 1);
        }
        
        // If it doesn't start with country code, add it
        if (strpos($phone, '880') !== 0) {
            $phone = '880' . $phone;
        }
        
        return $phone;
    }
}
