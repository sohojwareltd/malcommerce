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
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
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
    
    public function sponsors()
    {
        $sponsors = User::where('role', 'sponsor')
            ->withCount(['orders', 'referrals'])
            ->with(['orders' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->get()
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
            'phone' => 'required|string|max:20|unique:users,phone',
        ]);
        
        $phone = $this->normalizePhone($request->phone);
        
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
    
    public function showSponsor(User $sponsor)
    {
        // Ensure it's a sponsor
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        
        $sponsor->load(['orders.product', 'referrals']);
        
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
            'phone' => 'required|string|max:20|unique:users,phone,' . $sponsor->id,
        ]);
        
        $phone = $this->normalizePhone($request->phone);
        
        // Check if phone is already taken by another user
        if (User::where('phone', $phone)->where('id', '!=', $sponsor->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['phone' => 'This phone number is already registered.']);
        }
        
        $sponsor->update([
            'name' => $request->name,
            'phone' => $phone,
        ]);
        
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
}
