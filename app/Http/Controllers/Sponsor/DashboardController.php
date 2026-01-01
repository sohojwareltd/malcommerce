<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get orders from referrals (users sponsored by this sponsor) AND sponsor's own orders
        // Orders where user_id belongs to users where sponsor_id = current user's id OR user_id = current user's id
        $referralUserIds = $user->referrals()->pluck('id');
        $allUserIds = $referralUserIds->push($user->id)->unique();
        
        $stats = [
            'total_referrals' => $user->referrals()->count(),
            'total_orders' => Order::whereIn('user_id', $allUserIds)->count(),
            'pending_orders' => Order::whereIn('user_id', $allUserIds)->where('status', 'pending')->count(),
        ];
        
        $recentOrders = Order::whereIn('user_id', $allUserIds)
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($order) use ($user) {
                // Add order type: 'my_order' if it's sponsor's own order, 'referral_order' if from referral
                $order->order_type = $order->user_id == $user->id ? 'my_order' : 'referral_order';
                return $order;
            });
        
        // Build referrals query with search
        $referralsQuery = $user->referrals()->withCount('customerOrders as orders_count');
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $referralsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('affiliate_code', 'like', "%{$search}%");
            });
        }
        
        $referrals = $referralsQuery->latest()->take(10)->get();
        
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
            ->withCount('customerOrders as orders_count')
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
        
        // Handle photo upload with auto-resize
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            // Resize and store photo (400x400 pixels, 85% quality)
            $photoPath = \App\Services\ImageResizeService::resizeAndStore(
                $request->file('photo'),
                'photos',
                400,
                400,
                85
            );
            $data['photo'] = $photoPath;
        }
        
        $user->update($data);
        
        return redirect()->route('sponsor.dashboard')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Show the referral user details page
     */
    public function showReferral(User $referral)
    {
        $sponsor = Auth::user();
        
        // Ensure the referral belongs to the current sponsor
        if ($referral->sponsor_id !== $sponsor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load referral's customer orders and their products
        $referral->load(['customerOrders.product' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        // Calculate statistics
        $stats = [
            'total_orders' => $referral->customerOrders()->count(),
            'total_revenue' => $referral->customerOrders()->where('status', '!=', 'cancelled')->sum('total_price'),
            'pending_orders' => $referral->customerOrders()->where('status', 'pending')->count(),
            'delivered_orders' => $referral->customerOrders()->where('status', 'delivered')->count(),
        ];
        
        return view('sponsor.users.show', compact('referral', 'sponsor', 'stats'));
    }
    
    /**
     * Show the edit referral user page
     */
    public function editReferral(User $referral)
    {
        $sponsor = Auth::user();
        
        // Ensure the referral belongs to the current sponsor
        if ($referral->sponsor_id !== $sponsor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('sponsor.users.edit', compact('referral', 'sponsor'));
    }
    
    /**
     * Update a referral user
     */
    public function updateReferral(Request $request, User $referral)
    {
        $sponsor = Auth::user();
        
        // Ensure the referral belongs to the current sponsor
        if ($referral->sponsor_id !== $sponsor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = [
            'name' => $request->name,
            'address' => $request->address,
        ];
        
        // Handle photo upload with auto-resize
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($referral->photo && Storage::disk('public')->exists($referral->photo)) {
                Storage::disk('public')->delete($referral->photo);
            }
            
            // Resize and store photo (400x400 pixels, 85% quality)
            $photoPath = \App\Services\ImageResizeService::resizeAndStore(
                $request->file('photo'),
                'photos',
                400,
                400,
                85
            );
            $data['photo'] = $photoPath;
        }
        
        $referral->update($data);
        
        return redirect()->route('sponsor.dashboard')
            ->with('success', 'Referral user updated successfully!');
    }
    
    /**
     * Show all orders for the current sponsor with search and filters
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        
        // Get orders from referrals (users sponsored by this sponsor) AND sponsor's own orders
        $referralUserIds = $user->referrals()->pluck('id');
        $allUserIds = $referralUserIds->push($user->id)->unique();
        
        $query = Order::whereIn('user_id', $allUserIds)
            ->with(['product', 'user']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                                 ->orWhere('phone', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        // Add order type to each order
        $orders->getCollection()->transform(function($order) use ($user) {
            $order->order_type = $order->user_id == $user->id ? 'my_order' : 'referral_order';
            return $order;
        });
        
        // Calculate summary stats
        $stats = [
            'total_orders' => Order::whereIn('user_id', $allUserIds)->count(),
            'total_revenue' => Order::whereIn('user_id', $allUserIds)->where('status', '!=', 'cancelled')->sum('total_price'),
            'pending_orders' => Order::whereIn('user_id', $allUserIds)->where('status', 'pending')->count(),
            'delivered_orders' => Order::whereIn('user_id', $allUserIds)->where('status', 'delivered')->count(),
            'my_orders' => Order::where('user_id', $user->id)->count(),
            'referral_orders' => Order::whereIn('user_id', $referralUserIds)->count(),
        ];
        
        return view('sponsor.orders.index', compact('orders', 'stats'));
    }
}
