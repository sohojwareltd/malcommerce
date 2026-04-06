<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Order;
use App\Models\Product;
use App\Models\GalleryPhoto;
use App\Models\Purchase;
use App\Models\SponsorLevel;
use App\Models\User;
use App\Services\SponsorMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Resize and replace a user's profile photo before saving.
     */
    protected function resizeAndReplaceUserPhoto(Request $request, ?User $user, array &$data): void
    {
        if (!$request->hasFile('photo')) {
            return;
        }

        // Delete old photo if exists (prevents leaving unreferenced originals).
        if ($user && $user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $photoPath = \App\Services\ImageResizeService::resizeAndStore(
            $request->file('photo'),
            'photos',
            400,
            400,
            85
        );

        $data['photo'] = $photoPath;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Separate stats for my orders vs referral orders
        $myOrdersCount = Order::where('user_id', $user->id)->count();
        $referralOrdersCount = Order::where('sponsor_id', $user->id)->count();
        
        $stats = [
            'total_referrals' => $user->referrals()->count(),
            'my_orders' => $myOrdersCount,
            'referral_orders' => $referralOrdersCount,
        ];
        
        // Get recent orders (both my orders and referral orders)
        $myRecentOrders = Order::where('user_id', $user->id)
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($order) {
                $order->order_type = 'my_order';
                return $order;
            });
        
        $referralRecentOrders = Order::where('sponsor_id', $user->id)
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($order) {
                $order->order_type = 'referral_order';
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
        
        $affiliateLink = route('login') . '?ref=' . $user->affiliate_code;
        
        // Get all active products for affiliate links
        $products = Product::where('is_active', true)->orderBy('name')->get();
        
        // Recent gallery photos for quick preview (only this user's photos)
        $galleryPreviewPhotos = GalleryPhoto::with(['user', 'uploader'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        $recentDigitalOrders = Order::with('product')
            ->where('user_id', $user->id)
            ->whereHas('product', fn ($q) => $q->where('is_digital', true))
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('payment_method', 'bkash')
                        ->where('payment_status', 'completed');
                })->orWhere(function ($q) {
                    $q->where('payment_method', '!=', 'bkash')
                        ->whereIn('status', ['processing', 'shipped', 'delivered']);
                });
            })
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $purchasePendingOwnSum = (float) Purchase::query()
            ->where('submitted_by_sponsor_id', $user->id)
            ->where('kind', Purchase::KIND_OWN)
            ->where('status', Purchase::STATUS_PENDING)
            ->sum('amount');

        $purchasePendingTeamSum = (float) Purchase::query()
            ->where('submitted_by_sponsor_id', $user->id)
            ->where('kind', Purchase::KIND_TEAM)
            ->where('status', Purchase::STATUS_PENDING)
            ->sum('amount');

        $user->loadMissing('sponsorLevel');
        $sponsorMetrics = app(SponsorMetricsService::class)->dashboardMetrics($user);

        // Sum admin manual income where category is "Performance bonus" (matches admin preset; case-insensitive).
        $performanceBonusQuery = Earning::query()
            ->where('sponsor_id', $user->id)
            ->where('earning_type', 'manual_income');
        if (DB::connection()->getDriverName() === 'sqlite') {
            $performanceBonusQuery->whereRaw('LOWER(json_extract(meta, \'$.category\')) = ?', ['performance bonus']);
        } else {
            $performanceBonusQuery->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(meta, \'$.category\'))) = ?', ['performance bonus']);
        }
        $performanceBonusTotal = (float) $performanceBonusQuery->sum('amount');
        
        return view('sponsor.dashboard', [
            'stats' => $stats,
            'myRecentOrders' => $myRecentOrders,
            'referralRecentOrders' => $referralRecentOrders,
            'referrals' => $referrals,
            'affiliateLink' => $affiliateLink,
            'products' => $products,
            'galleryPreviewPhotos' => $galleryPreviewPhotos,
            'recentDigitalOrders' => $recentDigitalOrders,
            'purchasePendingOwnSum' => $purchasePendingOwnSum,
            'purchasePendingTeamSum' => $purchasePendingTeamSum,
            'sponsorMetrics' => $sponsorMetrics,
            'performanceBonusTotal' => $performanceBonusTotal,
        ]);
    }
    
    /**
     * Show all referrals with pagination and filters
     */
    public function referrals(Request $request)
    {
        $user = Auth::user();
        
        // Build referrals query
        $query = $user->referrals()
            ->withCount('customerOrders as orders_count')
            ->withSum([
                'purchasesAsBeneficiary as pending_purchase_amount' => function ($q) {
                    $q->where('status', Purchase::STATUS_PENDING);
                },
                'purchasesAsBeneficiary as purchase_amount' => function ($q) {
                    $q->where('status', Purchase::STATUS_ACCEPTED);
                },
                'purchasesAsBeneficiary as current_month_purchase_amount' => function ($q) {
                    $q->where('status', Purchase::STATUS_ACCEPTED)
                        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                },
            ], 'amount');
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('affiliate_code', 'like', "%{$search}%");
            });
        }
        
        // Get per page value from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        // Order by latest first
        $referrals = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        return view('sponsor.users.index', compact('referrals', 'user'));
    }
    
    /**
     * Show the add user page
     */
    public function createUser()
    {
        $user = Auth::user();
        return view('sponsor.users.create', compact('user'));
    }
    
    /**
     * Add a new user (referred by the current sponsor)
     */
    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email',
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'comment' => 'nullable|string|max:2000',
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
        
        $data = [
            'name' => $request->name,
            'phone' => $phone,
            'role' => 'sponsor', // All users are sponsors
            'sponsor_id' => $sponsor->id, // Referred by current sponsor
            'password' => null,
            'address' => $request->address,
            'comment' => $request->comment,
        ];

        $defaultLevel = SponsorLevel::defaultForNewSponsors();
        if ($defaultLevel) {
            $data['sponsor_level_id'] = $defaultLevel->id;
        }

        // Optional email for referred users
        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }
        
        // Handle photo upload with auto-resize
        try {
            $this->resizeAndReplaceUserPhoto($request, null, $data);
        } catch (\Exception $e) {
            \Log::error('Photo upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo. ' . $e->getMessage()
            ], 422);
        }
        
        // Create user with current sponsor as referrer
        $user = User::create($data);
        
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
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);
        
        $data = [
            'name' => $request->name,
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
        $this->resizeAndReplaceUserPhoto($request, $user, $data);
        
        $user->update($data);
        
        return redirect()->route('sponsor.profile.edit')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update sponsor password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // If user has a password, require current password; otherwise it's optional
        $rules = [
            'password' => 'required|min:6|confirmed',
        ];
        
        if ($user->password) {
            $rules['current_password'] = 'required';
        }
        
        $request->validate($rules);
        
        // Check current password if user has a password set
        if ($user->password && $request->filled('current_password') && !\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        // Update password (Laravel will auto-hash due to 'hashed' cast in User model)
        $user->update([
            'password' => $request->password,
        ]);
        
        return redirect()->route('sponsor.profile.edit')
            ->with('success', 'Password updated successfully!');
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
        
        // Load referral's customer orders, gallery, and their own referrals
        $referral->load([
            'customerOrders.product' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'galleryPhotos' => function ($query) {
                $query->with('uploader')->latest()->take(12);
            },
            'referrals' => function ($query) {
                $query->withCount('customerOrders')
                    ->orderByDesc('created_at')
                    ->take(50);
            },
        ]);
        
        // Calculate statistics
        $stats = [
            'total_orders' => $referral->customerOrders()->count(),
            'total_revenue' => $referral->customerOrders()->where('status', '!=', 'cancelled')->sum('total_price'),
            'pending_orders' => $referral->customerOrders()->where('status', 'pending')->count(),
            'delivered_orders' => $referral->customerOrders()->where('status', 'delivered')->count(),
        ];

        $purchaseSummary = [
            'pending_count' => Purchase::query()
                ->where('beneficiary_user_id', $referral->id)
                ->where('status', Purchase::STATUS_PENDING)
                ->count(),
            'pending_amount' => (float) Purchase::query()
                ->where('beneficiary_user_id', $referral->id)
                ->where('status', Purchase::STATUS_PENDING)
                ->sum('amount'),
            'accepted_count' => Purchase::query()
                ->where('beneficiary_user_id', $referral->id)
                ->where('status', Purchase::STATUS_ACCEPTED)
                ->count(),
            'accepted_amount' => (float) Purchase::query()
                ->where('beneficiary_user_id', $referral->id)
                ->where('status', Purchase::STATUS_ACCEPTED)
                ->sum('amount'),
            'canceled_count' => Purchase::query()
                ->where('beneficiary_user_id', $referral->id)
                ->where('status', Purchase::STATUS_CANCELED)
                ->count(),
            'canceled_amount' => (float) Purchase::query()
                ->where('beneficiary_user_id', $referral->id)
                ->where('status', Purchase::STATUS_CANCELED)
                ->sum('amount'),
            'current_month_amount' => (float) Purchase::query()
                ->where('beneficiary_user_id', $referral->id)
                ->where('status', Purchase::STATUS_ACCEPTED)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
        ];

        $incomeSummary = [
            'lifetime_earnings' => (float) Earning::query()
                ->where('sponsor_id', $referral->id)
                ->sum('amount'),
            'available_balance' => (float) ($referral->balance ?? 0),
        ];

        $recentPurchaseRequests = Purchase::query()
            ->where('beneficiary_user_id', $referral->id)
            ->with(['submittedBy', 'processedBy'])
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        return view('sponsor.users.show', compact(
            'referral',
            'sponsor',
            'stats',
            'purchaseSummary',
            'incomeSummary',
            'recentPurchaseRequests'
        ));
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
            'comment' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);
        
        $data = [
            'name' => $request->name,
            'address' => $request->address,
            'comment' => $request->comment,
        ];
        
        // Handle photo upload with auto-resize
        $this->resizeAndReplaceUserPhoto($request, $referral, $data);
        
        $referral->update($data);
        
        return redirect()->route('sponsor.dashboard')
            ->with('success', 'Referral user updated successfully!');
    }
    
    /**
     * Show my orders (where user_id = sponsor's id) - these don't count as revenue
     */
    public function myOrders(Request $request)
    {
        $user = Auth::user();
        
        $query = Order::where('user_id', $user->id)
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
        
        // Get per page value from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        
        // Calculate summary stats
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_amount' => Order::where('user_id', $user->id)->where('status', '!=', 'cancelled')->sum('total_price'),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
        ];
        
        return view('sponsor.orders.my-orders', compact('orders', 'stats'));
    }
    
    /**
     * Show referral orders (where sponsor_id = sponsor's id) - these count as revenue
     */
    public function referralOrders(Request $request)
    {
        $user = Auth::user();
        
        $query = Order::where('sponsor_id', $user->id)
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
        
        // Get per page value from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        
        // Calculate summary stats (these count as revenue)
        $stats = [
            'total_orders' => Order::where('sponsor_id', $user->id)->count(),
            'total_revenue' => Order::where('sponsor_id', $user->id)->where('status', '!=', 'cancelled')->sum('total_price'),
            'pending_orders' => Order::where('sponsor_id', $user->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('sponsor_id', $user->id)->where('status', 'delivered')->count(),
        ];
        
        return view('sponsor.orders.referral-orders', compact('orders', 'stats'));
    }
}
