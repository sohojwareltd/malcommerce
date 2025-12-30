<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_price'),
            'total_sponsors' => User::where('role', 'sponsor')->count(),
        ];
        
        $recentOrders = Order::with('product', 'sponsor')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
    
    public function categories()
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }
    
    public function createCategory()
    {
        return view('admin.categories.create');
    }
    
    public function storeCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:categories,slug',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);
            
            $data = $validated;
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            }
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('categories', $filename, 'public');
                $data['image'] = Storage::disk('public')->url($path);
            }
            
            $data['is_active'] = $request->has('is_active');
            $data['sort_order'] = $data['sort_order'] ?? 0;
            
            $category = Category::create($data);
            
            // If request expects JSON (for modal quick create)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category created successfully!',
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ]
                ]);
            }
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If request expects JSON, return JSON error response
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }
    }
    
    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    
    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $data = $request->all();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                $oldPath = str_replace('/storage/', '', parse_url($category->image, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }
            
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('categories', $filename, 'public');
            $data['image'] = Storage::disk('public')->url($path);
        }
        
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        
        $category->update($data);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }
    
    public function destroyCategory(Category $category)
    {
        // Delete image if exists
        if ($category->image) {
            $oldPath = str_replace('/storage/', '', parse_url($category->image, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
    
    public function orders(Request $request)
    {
        $query = Order::with('product', 'sponsor');
        
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
                  ->orWhereHas('sponsor', function($sponsorQuery) use ($search) {
                      $sponsorQuery->where('name', 'like', '%' . $search . '%');
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
        return view('admin.orders.index', compact('orders'));
    }
    
    public function showOrder(Order $order)
    {
        $order->load('product', 'sponsor');
        return view('admin.orders.show', compact('order'));
    }
    
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $order->status = $request->status;
        if ($request->filled('notes')) {
            $order->notes = $request->notes;
        }
        $order->save();
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully!');
    }
    
    public function sponsors(Request $request)
    {
        $query = User::where('role', 'sponsor')
            ->withCount(['orders', 'referrals'])
            ->with(['orders' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }]);
        
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
        
        $sponsors = $query->get()
            ->map(function($sponsor) {
                $sponsor->total_revenue = $sponsor->orders->sum('total_price');
                return $sponsor;
            });
            
        return view('admin.sponsors.index', compact('sponsors'));
    }
    
    public function createSponsor()
    {
        return view('admin.sponsors.create');
    }
    
    public function storeSponsor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);
        
        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => $e->getMessage()]);
        }
        
        // Check if user already exists with normalized phone
        if (User::where('phone', $phone)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'This phone number is already registered.']);
        }
        
        // Create sponsor user
        User::create([
            'name' => $request->name,
            'phone' => $phone,
            'role' => 'sponsor',
            'password' => null, // OTP-based auth doesn't need password
            // affiliate_code will be auto-generated in User model boot method
        ]);
        
        return redirect()->route('admin.sponsors.index')
            ->with('success', 'Sponsor created successfully!');
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
    
    public function showSponsor(User $sponsor)
    {
        // Ensure it's a sponsor
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        
        $sponsor->load(['orders.product', 'referrals' => function($query) {
            $query->withCount('orders');
        }]);
        
        // Get all active products for affiliate links
        $products = Product::where('is_active', true)->orderBy('name')->get();
        
        // Calculate statistics
        $stats = [
            'total_orders' => $sponsor->orders()->count(),
            'total_revenue' => $sponsor->orders()->where('status', '!=', 'cancelled')->sum('total_price'),
            'pending_orders' => $sponsor->orders()->where('status', 'pending')->count(),
            'delivered_orders' => $sponsor->orders()->where('status', 'delivered')->count(),
            'total_referrals' => $sponsor->referrals()->count(),
        ];
        
        return view('admin.sponsors.show', compact('sponsor', 'products', 'stats'));
    }
    
    public function editSponsor(User $sponsor)
    {
        // Ensure it's a sponsor
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        
        return view('admin.sponsors.edit', compact('sponsor'));
    }
    
    public function updateSponsor(Request $request, User $sponsor)
    {
        // Ensure it's a sponsor
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => $e->getMessage()]);
        }
        
        // Check if phone is already taken by another user
        if (User::where('phone', $phone)->where('id', '!=', $sponsor->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'This phone number is already registered.']);
        }
        
        $data = [
            'name' => $request->name,
            'phone' => $phone,
            'address' => $request->address,
        ];
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($sponsor->photo && Storage::disk('public')->exists($sponsor->photo)) {
                Storage::disk('public')->delete($sponsor->photo);
            }
            
            // Store new photo
            $photoPath = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $photoPath;
        }
        
        $sponsor->update($data);
        
        return redirect()->route('admin.sponsors.show', $sponsor)
            ->with('success', 'Sponsor updated successfully!');
    }
    
    public function destroySponsor(User $sponsor)
    {
        // Ensure it's a sponsor
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        
        $sponsor->delete();
        
        return redirect()->route('admin.sponsors.index')
            ->with('success', 'Sponsor deleted successfully!');
    }
    
    public function users(Request $request)
    {
        $query = User::where('role', 'admin');
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }
    
    public function createUser()
    {
        return view('admin.users.create');
    }
    
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
        ]);
        
        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => $e->getMessage()]);
        }
        
        // Check if user already exists with normalized phone
        if (User::where('phone', $phone)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'This phone number is already registered.']);
        }
        
        // Create admin user
        User::create([
            'name' => $request->name,
            'phone' => $phone,
            'role' => 'admin',
            'password' => null, // OTP-based auth doesn't need password
            'affiliate_code' => null, // Admins don't need affiliate codes
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user created successfully!');
    }
    
    public function editUser(User $user)
    {
        // Ensure it's an admin
        if ($user->role !== 'admin') {
            abort(404);
        }
        
        return view('admin.users.edit', compact('user'));
    }
    
    public function updateUser(Request $request, User $user)
    {
        // Ensure it's an admin
        if ($user->role !== 'admin') {
            abort(404);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
        ]);
        
        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => $e->getMessage()]);
        }
        
        // Check if phone is already taken by another user
        if (User::where('phone', $phone)->where('id', '!=', $user->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'This phone number is already registered.']);
        }
        
        $user->update([
            'name' => $request->name,
            'phone' => $phone,
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user updated successfully!');
    }
    
    public function destroyUser(User $user)
    {
        // Ensure it's an admin
        if ($user->role !== 'admin') {
            abort(404);
        }
        
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user deleted successfully!');
    }
    
    public function salesReport(Request $request)
    {
        $query = Order::with('product', 'sponsor')
            ->where('status', '!=', 'cancelled');
        
        // Date range filter
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        
        $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // Calculate statistics
        $stats = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_price'),
            'average_order_value' => $orders->count() > 0 ? $orders->sum('total_price') / $orders->count() : 0,
            'total_items_sold' => $orders->sum('quantity'),
            'by_status' => $orders->groupBy('status')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'revenue' => $group->sum('total_price'),
                ];
            }),
            'by_day' => $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function($group) {
                return [
                    'count' => $group->count(),
                    'revenue' => $group->sum('total_price'),
                ];
            }),
        ];
        
        return view('admin.reports.sales', compact('orders', 'stats', 'dateFrom', 'dateTo'));
    }
    
    public function settings()
    {
        return view('admin.settings');
    }
    
    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        
        // Handle color_palette as JSON
        if (isset($data['color_palette']) && is_array($data['color_palette'])) {
            Setting::set('color_palette', json_encode($data['color_palette']), 'json', 'design', 'Color palette for theme');
            unset($data['color_palette']);
        }
        
        // Handle hero_slider as JSON
        if (isset($data['hero_slider']) && is_array($data['hero_slider'])) {
            // Process slides and sync color picker values
            $slides = [];
            foreach ($data['hero_slider'] as $slide) {
                // Skip empty slides
                if (empty($slide['title']) && empty($slide['image'])) {
                    continue;
                }
                
                // Sync color picker with text input (use text input if provided, otherwise use color picker)
                if (isset($slide['title_color_text']) && !empty($slide['title_color_text'])) {
                    $slide['title_color'] = $slide['title_color_text'];
                }
                unset($slide['title_color_text']);
                
                if (isset($slide['subtitle_color_text']) && !empty($slide['subtitle_color_text'])) {
                    $slide['subtitle_color'] = $slide['subtitle_color_text'];
                }
                unset($slide['subtitle_color_text']);
                
                if (isset($slide['button_bg_color_text']) && !empty($slide['button_bg_color_text'])) {
                    $slide['button_bg_color'] = $slide['button_bg_color_text'];
                }
                unset($slide['button_bg_color_text']);
                
                if (isset($slide['button_text_color_text']) && !empty($slide['button_text_color_text'])) {
                    $slide['button_text_color'] = $slide['button_text_color_text'];
                }
                unset($slide['button_text_color_text']);
                
                $slides[] = $slide;
            }
            
            Setting::set('hero_slider', json_encode(array_values($slides)), 'json', 'design', 'Hero slider slides');
            unset($data['hero_slider']);
        }
        
        // Handle other settings
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
        
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Show admin profile edit form
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }
    
    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => $e->getMessage()]);
        }
        
        // Check if phone is already taken by another user
        if (User::where('phone', $phone)->where('id', '!=', $user->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'This phone number is already registered.']);
        }
        
        $data = [
            'name' => $request->name,
            'phone' => $phone,
            'address' => $request->address,
        ];
        
        // Only update email if provided
        if ($request->filled('email')) {
            $data['email'] = $request->email;
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
        
        return redirect()->to(route('admin.profile.edit') . '#profile')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Show change password form (redirects to profile page with password tab)
     */
    public function showChangePasswordForm()
    {
        return redirect()->to(route('admin.profile.edit') . '#password');
    }
    
    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // If user has a password, require current password; otherwise it's optional
        $rules = [
            'password' => 'required|min:8|confirmed',
        ];
        
        if ($user->password) {
            $rules['current_password'] = 'required';
        }
        
        $request->validate($rules);
        
        // Check current password if user has a password set
        if ($user->password && !\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        $user->update([
            'password' => bcrypt($request->password),
        ]);
        
        return redirect()->to(route('admin.profile.edit') . '#password')
            ->with('success', 'Password changed successfully!');
    }
}
