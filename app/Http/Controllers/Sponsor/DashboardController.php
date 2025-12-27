<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'phone' => 'required|string|max:20',
        ]);
        
        $sponsor = Auth::user();
        
        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
        
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
     * Normalize and validate Bangladesh phone number
     * Formats: 01795560431 -> 8801795560431, 8801795560431 -> 8801795560431, +8801795560431 -> 8801795560431
     *
     * @param string $phone Phone number in any format
     * @return string Normalized phone number (880XXXXXXXXXX)
     * @throws \Exception If phone number is invalid
     */
    protected function normalizePhone($phone)
    {
        if (empty($phone)) {
            throw new \Exception('Phone number is required');
        }

        // Remove all non-numeric characters except + (we'll handle + separately)
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove + if present
        $phone = str_replace('+', '', $phone);
        
        // If it starts with 880, validate format
        if (strpos($phone, '880') === 0) {
            // Validate Bangladesh mobile format: 880 + 1 + 9 digits = 13 digits total
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
        }
        
        // If it starts with 0, replace with 880
        if (strpos($phone, '0') === 0) {
            $phone = '880' . substr($phone, 1);
            // Validate: should be 13 digits total and 4th digit should be 1
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
        }
        
        // If it doesn't start with 0 or 880, add 880 prefix
        $phone = '880' . $phone;
        // Validate: should be 13 digits total and 4th digit should be 1
        if (strlen($phone) === 13 && $phone[3] === '1') {
            return $phone;
        }
        
        throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
    }
    
    /**
     * Show the profile edit page
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('sponsor.profile.edit', compact('user'));
    }
    
    /**
     * Update the user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
        ];
        
        // Handle phone normalization
        try {
            $data['phone'] = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => $e->getMessage()]);
        }
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            // Store new photo
            $photoPath = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $photoPath;
        }
        
        $user->update($data);
        
        return redirect()->route('sponsor.dashboard')
            ->with('success', 'Profile updated successfully!');
    }
}
