<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Expense;
use App\Models\JobApplication;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WorkshopEnrollment;
use App\Models\Setting;
use App\Models\SponsorIncome;
use App\Models\SponsorLevel;
use App\Models\SponsorLevelHistory;
use App\Services\EarningService;
use App\Services\SponsorMetricsService;
use App\Services\SmsService;
use App\Services\SteadfastService;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class DashboardController extends Controller
{
    public function index()
    {
        // Use same logic as sales report: last 30 days, exclude cancelled
        $dateFrom = now()->subDays(30)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $ordersQuery = Order::with('product')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('status', '!=', 'cancelled');
        $orders = $ordersQuery->get();

        $deliveredOrders = $orders->where('status', 'delivered');
        $revenue = $deliveredOrders->sum('total_price');
        $pendingRevenue = $orders->whereIn('status', ['pending', 'processing', 'shipped'])->sum('total_price');
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $profit = $revenue - $totalExpenses;

        $stats = [
            'total_orders' => $orders->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'digital_products' => Product::where('is_digital', true)->count(),
            'physical_products' => Product::where('is_digital', false)->count(),
            'job_applications' => JobApplication::count(),
            'workshop_enrollments' => WorkshopEnrollment::count(),
            'revenue' => $revenue,
            'pending_revenue' => $pendingRevenue,
            'total_expenses' => $totalExpenses,
            'profit' => $profit,
            'average_order_value' => $deliveredOrders->count() > 0 ? $revenue / $deliveredOrders->count() : 0,
            'total_items_sold' => $orders->sum('quantity'),
            'total_sponsors' => User::where('role', 'sponsor')->count(),
            'pending_purchases' => Purchase::where('status', Purchase::STATUS_PENDING)->count(),
        ];

        $recentOrders = Order::with('product', 'sponsor')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'dateFrom', 'dateTo'));
    }
    
    public function categories(Request $request)
    {
        $query = Category::query();
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Get per page value from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        // Order by sort_order first, then by latest created
        $categories = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
            
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
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    public function restoreCategory(Request $request)
    {
        $category = Category::withTrashed()->findOrFail($request->route('category'));
        $this->authorize('restore', $category);
        $category->restore();
        return redirect()->route('admin.categories.index')->with('success', 'Category restored successfully!');
    }

    public function forceDestroyCategory(Request $request)
    {
        $category = Category::withTrashed()->findOrFail($request->route('category'));
        if (!$category->trashed()) {
            return redirect()->back()->with('error', 'Only soft-deleted categories can be permanently removed.');
        }
        $this->authorize('forceDelete', $category);
        try {
            if ($category->image) {
                $oldPath = str_replace('/storage/', '', parse_url($category->image, PHP_URL_PATH) ?? '');
                if ($oldPath !== '') {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $category->forceDelete();
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Cannot delete permanently: this category is still referenced in a way that blocks removal.');
        }

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', 'Category permanently deleted.');
    }
    
    public function orders(Request $request)
    {
        $query = Order::query();
        if ($request->boolean('trashed')) {
            $query->onlyTrashed()->with(['product' => fn ($q) => $q->withTrashed(), 'sponsor' => fn ($q) => $q->withTrashed()]);
        } else {
            $query->with('product', 'sponsor');
        }
        
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

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by product type (physical/digital)
        if ($request->filled('product_type')) {
            $isDigital = $request->product_type === 'digital';
            $productQuery = $request->boolean('trashed')
                ? fn ($q) => $q->withTrashed()->where('is_digital', $isDigital)
                : fn ($q) => $q->where('is_digital', $isDigital);
            $query->whereHas('product', $productQuery);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $categoryQuery = $request->boolean('trashed')
                ? fn ($q) => $q->withTrashed()->where('category_id', $request->category_id)
                : fn ($q) => $q->where('category_id', $request->category_id);
            $query->whereHas('product', $categoryQuery);
        }
        
        // Get per page value from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('admin.orders.index', compact('orders', 'products', 'categories'));
    }

    /**
     * Orders index pre-filtered to physical products.
     */
    public function ordersPhysical(Request $request)
    {
        $request->merge(['product_type' => 'physical']);
        return $this->orders($request);
    }

    /**
     * Orders index pre-filtered to digital products.
     */
    public function ordersDigital(Request $request)
    {
        $request->merge(['product_type' => 'digital']);
        return $this->orders($request);
    }
    
    public function showOrder(Order $order)
    {
        $order->load([
            'product',
            'sponsor',
            'logs.admin',
        ]);
        return view('admin.orders.show', compact('order'));
    }
    
    public function updateOrderStatus(Request $request, Order $order, EarningService $earningService)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        $order->status = $newStatus;
        if ($request->filled('notes')) {
            $order->notes = $request->notes;
        }
        $order->save();

        // Log status change with admin info
        if ($oldStatus !== $newStatus) {
            OrderLog::create([
                'order_id' => $order->id,
                'admin_id' => Auth::id(),
                'type' => 'status_changed',
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'notes' => $request->notes,
                'meta' => null,
            ]);
        }
        
        // Create earnings when order is marked as delivered (only if it wasn't already delivered)
        if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
            // Check if earnings already exist for this order to prevent duplicates
            $existingEarnings = $order->earnings()->count();
            
            if ($existingEarnings === 0) {
                try {
                    // Load relationships
                    $order->load(['product', 'user', 'sponsor']);
                    
                    if ($order->product && $order->user) {
                        // Create cashback earning for the customer
                        $earningService->createCashbackEarning($order, $order->product, $order->user);
                        
                        // Create referral commission for sponsor (if present)
                        if ($order->sponsor_id && $order->sponsor) {
                            $earningService->createReferralEarningsWithLevels($order, $order->product, $order->sponsor, $order->user);
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error('Earning creation failed when marking order as delivered', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Do not fail the status update if earnings fail
                }
            }
        }

        // Send status update SMS if status actually changed
        if ($oldStatus !== $newStatus) {
            try {
                $order->loadMissing('product');
                $message = $this->buildStatusSmsMessage($order, $newStatus);
                if ($message) {
                    $smsService = app(SmsService::class);
                    $smsResult = $smsService->send($order->customer_phone, $message);

                    if (!$smsResult['success']) {
                        \Log::warning('Failed to send status update SMS', [
                            'order_id' => $order->id,
                            'phone' => $order->customer_phone,
                            'status' => $newStatus,
                            'error' => $smsResult['error'] ?? 'Unknown error',
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('SMS sending exception on status change', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Auto-create Steadfast parcel when status changes to shipped
            if ($newStatus === 'shipped') {
                try {
                    $steadfast = app(SteadfastService::class);
                    if ($steadfast->isConfigured()) {
                        $steadfast->createOrder($order);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Auto-create Steadfast parcel failed on status change', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully!');
    }

    /**
     * Create Steadfast (Packzy) parcel for an order.
     */
    public function createSteadfastParcel(Order $order, SteadfastService $steadfast)
    {
        $result = $steadfast->createOrder($order);

        if ($result['success']) {
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Parcel created successfully! Tracking code: ' . ($result['tracking_code'] ?? 'N/A'));
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('error', $result['error'] ?? 'Failed to create parcel');
    }

    /**
     * Refresh Steadfast delivery status for an order.
     */
    public function refreshSteadfastStatus(Order $order, SteadfastService $steadfast)
    {
        if (!$order->steadfast_consignment_id && !$order->steadfast_tracking_code) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'No Steadfast parcel found for this order.');
        }

        $invoice = 'ORD-' . $order->order_number . '-' . $order->id;
        $result = $steadfast->getStatus($invoice);

        if ($result['success']) {
            $order->update(['steadfast_delivery_status' => $result['delivery_status']]);
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Status refreshed: ' . $result['delivery_status']);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('error', $result['error'] ?? 'Failed to fetch status');
    }

    /**
     * Remove Steadfast parcel info from an order.
     */
    public function removeSteadfastInfo(Order $order)
    {
        $order->update([
            'steadfast_consignment_id' => null,
            'steadfast_tracking_code' => null,
            'steadfast_delivery_status' => null,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Steadfast info removed from order.');
    }
    
    public function editOrder(Order $order)
    {
        $order->load('product');
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.edit', compact('order', 'products'));
    }
    
    public function updateOrder(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'delivery_charge' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);
        
        // Normalize phone number
        try {
            $normalizedPhone = $this->normalizePhone($request->customer_phone);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['customer_phone' => $e->getMessage()]);
        }
        
        // Calculate the base total (unit_price * quantity + delivery_charge)
        $subtotal = $request->unit_price * $request->quantity;
        $deliveryCharge = $request->delivery_charge ?? 0;
        $calculatedTotal = $subtotal + $deliveryCharge;
        
        // Get the original total price from the order
        $originalTotal = $order->total_price;
        $newTotal = $request->total_price;
        
        // Calculate discount or additional fees based on difference from original total
        $discount = 0;
        $additionalFees = 0;
        
        if ($newTotal < $originalTotal) {
            // If new total is less, the difference is a discount
            $discount = $originalTotal - $newTotal;
        } elseif ($newTotal > $originalTotal) {
            // If new total is more, the difference is additional fees
            $additionalFees = $newTotal - $originalTotal;
        }
        // If equal, both remain 0
        
        // Update order
        $order->update([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'delivery_charge' => $deliveryCharge,
            'total_price' => $newTotal,
            'discount' => $discount,
            'additional_fees' => $additionalFees,
            'customer_name' => $request->customer_name,
            'customer_phone' => $normalizedPhone,
            'address' => $request->address,
        ]);

        // Log order update
        OrderLog::create([
            'order_id' => $order->id,
            'admin_id' => Auth::id(),
            'type' => 'order_updated',
            'from_status' => $order->status,
            'to_status' => $order->status,
            'notes' => 'Order details updated.',
            'meta' => null,
        ]);
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully!');
    }
    
    public function sponsors(Request $request)
    {
        $query = User::where('role', 'sponsor');
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }
        $with = ['sponsor', 'orders' => fn ($q) => $q->where('status', '!=', 'cancelled')];
        if ($request->boolean('trashed')) {
            $with['sponsor'] = fn ($q) => $q->withTrashed();
        }
        $query->withCount(['orders', 'referrals'])->with($with);
        
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
        
        // Order by latest first (created_at desc)
        $sponsors = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
            
        // Calculate total revenue for each sponsor
        $sponsors->getCollection()->transform(function($sponsor) {
            $sponsor->total_revenue = $sponsor->orders->sum('total_price') ?? 0;
                return $sponsor;
            });

        $bulkReferrerOptions = collect();
        $bulkSponsorLevels = collect();
        if (!$request->boolean('trashed') && $request->user()->can('sponsors.update')) {
            $bulkReferrerOptions = User::where('role', 'sponsor')
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'affiliate_code', 'phone']);
            $bulkSponsorLevels = SponsorLevel::query()->orderBy('rank')->get();
        }

        return view('admin.sponsors.index', compact('sponsors', 'bulkReferrerOptions', 'bulkSponsorLevels'));
    }

    public function bulkSetSponsorReferrer(Request $request)
    {
        $request->validate([
            'bulk_action' => 'required|in:assign,clear',
            'sponsor_ids' => 'required|array|min:1',
            'sponsor_ids.*' => 'required|integer|exists:users,id',
            'referrer_sponsor_id' => [
                'nullable',
                'required_if:bulk_action,assign',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'sponsor')),
            ],
        ]);

        $ids = array_values(array_unique(array_map('intval', $request->sponsor_ids)));

        $validIds = User::where('role', 'sponsor')->whereIn('id', $ids)->pluck('id')->all();
        if (count($validIds) !== count($ids)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['sponsor_ids' => 'One or more selected users are not valid partners.']);
        }

        if ($request->bulk_action === 'clear') {
            User::whereIn('id', $ids)->update(['sponsor_id' => null]);

            return redirect()->back()->with('success', 'Referrer cleared for '.count($ids).' partner(s).');
        }

        $referrerId = (int) $request->referrer_sponsor_id;
        $targetIds = array_values(array_diff($ids, [$referrerId]));

        if (count($targetIds) === 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['referrer_sponsor_id' => 'None of the selected partners can have themselves as referrer.']);
        }

        User::whereIn('id', $targetIds)->update(['sponsor_id' => $referrerId]);
        $skipped = count($ids) - count($targetIds);
        $msg = 'Referrer updated for '.count($targetIds).' partner(s).';
        if ($skipped > 0) {
            $msg .= ' Skipped '.$skipped.' (cannot refer themselves).';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function bulkSetSponsorLevel(Request $request)
    {
        $request->validate([
            'bulk_level_action' => 'required|in:assign,clear',
            'sponsor_ids' => 'required|array|min:1',
            'sponsor_ids.*' => 'required|integer|exists:users,id',
            'bulk_sponsor_level_id' => [
                'nullable',
                'required_if:bulk_level_action,assign',
                'exists:sponsor_levels,id',
            ],
        ], [
            'bulk_sponsor_level_id.required_if' => 'Choose a level to assign.',
        ]);

        $ids = array_values(array_unique(array_map('intval', $request->sponsor_ids)));

        $validIds = User::where('role', 'sponsor')->whereIn('id', $ids)->pluck('id')->all();
        if (count($validIds) !== count($ids)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['sponsor_ids' => 'One or more selected users are not valid partners.']);
        }

        $toLevelId = $request->bulk_level_action === 'assign'
            ? (int) $request->bulk_sponsor_level_id
            : null;

        $updated = 0;

        DB::transaction(function () use ($validIds, $toLevelId, &$updated) {
            $users = User::whereIn('id', $validIds)->lockForUpdate()->get();
            foreach ($users as $user) {
                if ($user->role !== 'sponsor') {
                    continue;
                }
                $from = $user->sponsor_level_id;
                $normFrom = $from === null ? null : (int) $from;
                $normTo = $toLevelId === null ? null : (int) $toLevelId;
                if ($normFrom === $normTo) {
                    continue;
                }
                $user->update(['sponsor_level_id' => $toLevelId]);
                SponsorLevelHistory::create([
                    'user_id' => $user->id,
                    'from_sponsor_level_id' => $from,
                    'to_sponsor_level_id' => $toLevelId,
                    'changed_by' => Auth::id(),
                ]);
                $updated++;
            }
        });

        if ($updated === 0) {
            return redirect()->back()->with('success', 'No changes: selected partners already had that level.');
        }

        $msg = $request->bulk_level_action === 'clear'
            ? "Level cleared for {$updated} partner(s)."
            : "Level updated for {$updated} partner(s).";

        return redirect()->back()->with('success', $msg);
    }
    
    public function createSponsor()
    {
        $referrerOptions = User::where('role', 'sponsor')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'affiliate_code', 'phone']);

        return view('admin.sponsors.create', compact('referrerOptions'));
    }

    /**
     * Build SMS message for a status change using product-level templates.
     */
    protected function buildStatusSmsMessage(Order $order, string $status): ?string
    {
        $product = $order->product;
        $templates = $product->sms_templates ?? [];
        $template = $templates[$status] ?? null;

        $defaultMessage = "Your order #{$order->order_number} is now " . ucfirst($status) . ".";
        $message = $template ?: $defaultMessage;

        $productName = $product ? $product->name : 'product';

        $replacements = [
            '{order_number}' => $order->order_number,
            '{customer_name}' => $order->customer_name,
            '{product_name}' => $productName,
            '{status}' => ucfirst($status),
            '{quantity}' => $order->quantity,
            '{total_price}' => number_format($order->total_price, 0),
            '{delivery_charge}' => number_format($order->delivery_charge ?? 0, 0),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
    
    public function storeSponsor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'comment' => 'nullable|string|max:2000',
            'sponsor_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'sponsor')),
            ],
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
        
        $data = [
            'name' => $request->name,
            'phone' => $phone,
            'role' => 'sponsor',
            'password' => null, // OTP-based auth doesn't need password
            'address' => $request->address,
            'comment' => $request->comment,
            'sponsor_id' => $request->filled('sponsor_id') ? (int) $request->sponsor_id : null,
            // affiliate_code will be auto-generated in User model boot method
        ];

        $defaultLevel = SponsorLevel::defaultForNewSponsors();
        if ($defaultLevel) {
            $data['sponsor_level_id'] = $defaultLevel->id;
        }
        
        // Handle photo upload with auto-resize
        if ($request->hasFile('photo')) {
            try {
                $this->resizeAndReplaceUserPhoto($request, null, $data);
            } catch (\Exception $e) {
                \Log::error('Photo upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['photo' => 'Failed to upload photo. ' . $e->getMessage()]);
            }
        }
        
        // Create sponsor user
        User::create($data);
        
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
    
    public function showSponsor(User $sponsor)
    {
        // Ensure it's a sponsor
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        
        $sponsor->load([
            'sponsor',
            'sponsorLevel',
            'sponsorLevelHistories.fromLevel',
            'sponsorLevelHistories.toLevel',
            'sponsorLevelHistories.changedBy',
            'orders.product',
            'referrals' => function($query) {
                $query->withCount('orders');
            },
            'createdFromOrder.product',
        ]);
        
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

        $sponsor->refresh();

        $lifetimeEarnings = (float) Earning::where('sponsor_id', $sponsor->id)->sum('amount');

        $earningsByType = Earning::query()
            ->where('sponsor_id', $sponsor->id)
            ->selectRaw('earning_type, SUM(amount) as total')
            ->groupBy('earning_type')
            ->pluck('total', 'earning_type')
            ->map(fn ($v) => (float) $v);

        $earningTypeLabels = [
            'referral' => 'Referral commissions',
            'cashback' => 'Cashback',
            'purchase' => 'Approved purchase commissions',
            'manual_income' => 'Admin manual income',
        ];

        $manualIncomes = SponsorIncome::query()
            ->where('sponsor_id', $sponsor->id)
            ->with('creator')
            ->orderByDesc('created_at')
            ->limit(40)
            ->get();

        $sponsorIncomeCategorySuggestions = [
            'Performance bonus',
            'Monthly incentive',
            'Promotion reward',
            'Correction / adjustment',
            'Contest or challenge prize',
            'Referral program bonus',
            'Welcome bonus',
            'Training completion bonus',
            'Other',
        ];

        $recentEarnings = Earning::query()
            ->where('sponsor_id', $sponsor->id)
            ->with(['order', 'referral'])
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        $withdrawalSummary = [
            'approved_total' => (float) Withdrawal::where('sponsor_id', $sponsor->id)
                ->where('status', Withdrawal::STATUS_APPROVED)
                ->sum('amount'),
            'in_queue_total' => (float) Withdrawal::where('sponsor_id', $sponsor->id)
                ->whereIn('status', Withdrawal::activeStatuses())
                ->sum('amount'),
            'in_queue_count' => Withdrawal::where('sponsor_id', $sponsor->id)
                ->whereIn('status', Withdrawal::activeStatuses())
                ->count(),
            'cancelled_total' => (float) Withdrawal::where('sponsor_id', $sponsor->id)
                ->where('status', Withdrawal::STATUS_CANCELLED)
                ->sum('amount'),
        ];

        $purchaseSubmitted = [
            'pending_count' => $sponsor->purchasesSubmitted()->where('status', Purchase::STATUS_PENDING)->count(),
            'pending_amount' => (float) $sponsor->purchasesSubmitted()->where('status', Purchase::STATUS_PENDING)->sum('amount'),
            'accepted_count' => $sponsor->purchasesSubmitted()->where('status', Purchase::STATUS_ACCEPTED)->count(),
            'canceled_count' => $sponsor->purchasesSubmitted()->where('status', Purchase::STATUS_CANCELED)->count(),
        ];

        $purchaseAsBeneficiary = [
            'pending_count' => $sponsor->purchasesAsBeneficiary()->where('status', Purchase::STATUS_PENDING)->count(),
            'pending_amount' => (float) $sponsor->purchasesAsBeneficiary()->where('status', Purchase::STATUS_PENDING)->sum('amount'),
            'accepted_count' => $sponsor->purchasesAsBeneficiary()->where('status', Purchase::STATUS_ACCEPTED)->count(),
            'canceled_count' => $sponsor->purchasesAsBeneficiary()->where('status', Purchase::STATUS_CANCELED)->count(),
        ];

        $recentPurchases = Purchase::query()
            ->where(function ($q) use ($sponsor) {
                $q->where('submitted_by_sponsor_id', $sponsor->id)
                    ->orWhere('beneficiary_user_id', $sponsor->id);
            })
            ->with(['submittedBy', 'beneficiary'])
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $sponsorMetrics = app(SponsorMetricsService::class)->dashboardMetrics($sponsor);

        return view('admin.sponsors.show', compact(
            'sponsor',
            'products',
            'stats',
            'lifetimeEarnings',
            'earningsByType',
            'earningTypeLabels',
            'recentEarnings',
            'withdrawalSummary',
            'purchaseSubmitted',
            'purchaseAsBeneficiary',
            'recentPurchases',
            'sponsorMetrics',
            'manualIncomes',
            'sponsorIncomeCategorySuggestions',
        ));
    }

    public function storeSponsorIncome(Request $request, User $sponsor, EarningService $earningService)
    {
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'category_preset' => 'nullable|string|max:255',
            'category_custom' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $preset = trim((string) ($data['category_preset'] ?? ''));
        $custom = trim((string) ($data['category_custom'] ?? ''));
        if ($custom !== '') {
            $category = $custom;
        } elseif ($preset !== '') {
            $category = $preset;
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'category_preset' => 'Choose a category from the list or type a custom category below.',
                ]);
        }

        try {
            $earningService->createManualSponsorIncome(
                $sponsor,
                (float) $data['amount'],
                $category,
                isset($data['notes']) ? trim((string) $data['notes']) : null,
                Auth::id(),
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Income recorded and sponsor balance updated.');
    }
    
    public function editSponsor(User $sponsor)
    {
        // Ensure it's a sponsor
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        
        $referrerOptions = User::where('role', 'sponsor')
            ->where('id', '!=', $sponsor->id)
            ->where(function ($q) use ($sponsor) {
                $q->whereNull('deleted_at');
                if ($sponsor->sponsor_id) {
                    $q->orWhere('id', $sponsor->sponsor_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'affiliate_code', 'phone', 'deleted_at']);

        $sponsorLevels = SponsorLevel::query()->orderBy('rank')->get();

        return view('admin.sponsors.edit', compact('sponsor', 'referrerOptions', 'sponsorLevels'));
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'comment' => 'nullable|string|max:2000',
            'sponsor_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'sponsor')),
                Rule::notIn([$sponsor->id]),
            ],
            'sponsor_level_id' => ['nullable', 'exists:sponsor_levels,id'],
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
            'comment' => $request->comment,
            'sponsor_id' => $request->filled('sponsor_id') ? (int) $request->sponsor_id : null,
            'sponsor_level_id' => $request->filled('sponsor_level_id') ? (int) $request->sponsor_level_id : null,
        ];
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $this->resizeAndReplaceUserPhoto($request, $sponsor, $data);
        }

        $fromLevelId = $sponsor->sponsor_level_id;
        $sponsor->update($data);
        $sponsor->refresh();

        if ($fromLevelId !== $sponsor->sponsor_level_id) {
            SponsorLevelHistory::create([
                'user_id' => $sponsor->id,
                'from_sponsor_level_id' => $fromLevelId,
                'to_sponsor_level_id' => $sponsor->sponsor_level_id,
                'changed_by' => Auth::id(),
            ]);
        }
        
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

    public function restoreSponsor(Request $request)
    {
        $sponsor = User::withTrashed()->findOrFail($request->route('sponsor'));
        if ($sponsor->role !== 'sponsor') abort(404);
        $this->authorize('restore', $sponsor);
        $sponsor->restore();
        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor restored successfully!');
    }

    public function forceDestroySponsor(Request $request)
    {
        $sponsor = User::withTrashed()->findOrFail($request->route('sponsor'));
        if ($sponsor->role !== 'sponsor') {
            abort(404);
        }
        if (!$sponsor->trashed()) {
            return redirect()->back()->with('error', 'Only soft-deleted partners can be permanently removed.');
        }
        $this->authorize('forceDelete', $sponsor);
        try {
            if ($sponsor->photo && Storage::disk('public')->exists($sponsor->photo)) {
                Storage::disk('public')->delete($sponsor->photo);
            }
            $sponsor->forceDelete();
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Cannot delete permanently: this partner is still referenced (e.g. orders or downline). Remove or reassign those records first.');
        }

        return redirect()->route('admin.sponsors.index', ['trashed' => 1])
            ->with('success', 'Partner permanently deleted.');
    }
    
    public function users(Request $request)
    {
        $query = User::where('role', 'admin');
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Get per page value from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $users = $query->with('roles')->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }
    
    public function createUser()
    {
        $roles = \Spatie\Permission\Models\Role::where('guard_name', config('auth.defaults.guard'))
            ->orderBy('name')
            ->get();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'role_id' => 'required|exists:roles,id',
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

        $role = \Spatie\Permission\Models\Role::findOrFail($request->role_id);
        if ($role->guard_name !== config('auth.defaults.guard')) {
            abort(422, 'Invalid role.');
        }

        // Create admin user
        $user = User::create([
            'name' => $request->name,
            'phone' => $phone,
            'role' => 'admin',
            'password' => null, // OTP-based auth doesn't need password
            'affiliate_code' => null, // Admins don't need affiliate codes
        ]);

        $user->assignRole($role);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin user created successfully!');
    }
    
    public function editUser(User $user)
    {
        // Ensure it's an admin
        if ($user->role !== 'admin') {
            abort(404);
        }

        $roles = \Spatie\Permission\Models\Role::where('guard_name', config('auth.defaults.guard'))
            ->orderBy('name')
            ->get();
        return view('admin.users.edit', compact('user', 'roles'));
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
            'role_id' => 'required|exists:roles,id',
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

        $role = \Spatie\Permission\Models\Role::findOrFail($request->role_id);
        if ($role->guard_name !== config('auth.defaults.guard')) {
            abort(422, 'Invalid role.');
        }

        $user->update([
            'name' => $request->name,
            'phone' => $phone,
        ]);

        $user->syncRoles([$role]);

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

    public function restoreUser(Request $request)
    {
        $user = User::withTrashed()->findOrFail($request->route('user'));
        if ($user->role !== 'admin') abort(404);
        $this->authorize('restore', $user);
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'Admin user restored successfully!');
    }

    public function forceDestroyUser(Request $request)
    {
        $user = User::withTrashed()->findOrFail($request->route('user'));
        if ($user->role !== 'admin') {
            abort(404);
        }
        if (!$user->trashed()) {
            return redirect()->back()->with('error', 'Only soft-deleted admin users can be permanently removed.');
        }
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot permanently delete your own account.');
        }
        $this->authorize('forceDelete', $user);
        try {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->forceDelete();
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Cannot delete permanently: this user is still referenced elsewhere.');
        }

        return redirect()->route('admin.users.index', ['trashed' => 1])
            ->with('success', 'Admin user permanently deleted.');
    }
    
    public function salesReport(Request $request)
    {
        $query = Order::with('product', 'sponsor');

        // Date range filter
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'cancelled');
        }

        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Product type filter (physical / digital)
        if ($request->filled('product_type')) {
            $isDigital = $request->product_type === 'digital';
            $query->whereHas('product', fn ($q) => $q->where('is_digital', $isDigital));
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'is_digital']);

        // Revenue = delivered only. Pending revenue = pending, processing, shipped (excl. cancelled)
        $deliveredOrders = $orders->where('status', 'delivered');
        $pendingOrders = $orders->whereIn('status', ['pending', 'processing', 'shipped']);
        $revenue = $deliveredOrders->sum('total_price');
        $pendingRevenue = $pendingOrders->sum('total_price');

        // Total expenses in date range (for profit calculation)
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $profit = $revenue - $totalExpenses;

        // Calculate statistics
        $stats = [
            'total_orders' => $orders->count(),
            'revenue' => $revenue,
            'pending_revenue' => $pendingRevenue,
            'total_expenses' => $totalExpenses,
            'profit' => $profit,
            'average_order_value' => $deliveredOrders->count() > 0 ? $revenue / $deliveredOrders->count() : 0,
            'total_items_sold' => $orders->sum('quantity'),
            'by_status' => $orders->groupBy('status')->map(function ($group) {
                $isDelivered = $group->first()->status === 'delivered';
                return [
                    'count' => $group->count(),
                    'revenue' => $isDelivered ? $group->sum('total_price') : 0,
                    'pending_revenue' => !$isDelivered && $group->first()->status !== 'cancelled' ? $group->sum('total_price') : 0,
                ];
            }),
            'by_product' => $orders->groupBy('product_id')->map(function ($group) {
                $product = $group->first()->product;
                $delivered = $group->where('status', 'delivered');
                $pending = $group->whereIn('status', ['pending', 'processing', 'shipped']);
                return [
                    'name' => $product?->name ?? 'Unknown',
                    'count' => $group->count(),
                    'quantity' => $group->sum('quantity'),
                    'revenue' => $delivered->sum('total_price'),
                    'pending_revenue' => $pending->sum('total_price'),
                ];
            })->sortByDesc(fn ($r) => $r['revenue'] + $r['pending_revenue']),
            'by_product_type' => collect([
                'physical' => [
                    'name' => 'Physical',
                    'orders' => $orders->filter(fn ($o) => $o->product && !$o->product->is_digital),
                ],
                'digital' => [
                    'name' => 'Digital',
                    'orders' => $orders->filter(fn ($o) => $o->product && $o->product->is_digital),
                ],
            ])->map(fn ($data) => [
                'name' => $data['name'],
                'count' => $data['orders']->count(),
                'quantity' => $data['orders']->sum('quantity'),
                'revenue' => $data['orders']->where('status', 'delivered')->sum('total_price'),
                'pending_revenue' => $data['orders']->whereIn('status', ['pending', 'processing', 'shipped'])->sum('total_price'),
            ]),
            'by_day' => $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))->map(fn ($group) => [
                'count' => $group->count(),
                'revenue' => $group->where('status', 'delivered')->sum('total_price'),
                'pending_revenue' => $group->whereIn('status', ['pending', 'processing', 'shipped'])->sum('total_price'),
            ]),
        ];

        return view('admin.reports.sales', compact('orders', 'stats', 'dateFrom', 'dateTo', 'products'));
    }

    public function exportSalesReport(Request $request)
    {
        $this->authorize('reports.sales');
        $query = Order::with('product', 'sponsor');

        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'cancelled');
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('product_type')) {
            $isDigital = $request->product_type === 'digital';
            $query->whereHas('product', fn ($q) => $q->where('is_digital', $isDigital));
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $deliveredOrders = $orders->where('status', 'delivered');
        $pendingOrders = $orders->whereIn('status', ['pending', 'processing', 'shipped']);
        $revenue = $deliveredOrders->sum('total_price');
        $pendingRevenue = $pendingOrders->sum('total_price');
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        $profit = $revenue - $totalExpenses;

        $stats = [
            'total_orders' => $orders->count(),
            'revenue' => $revenue,
            'pending_revenue' => $pendingRevenue,
            'total_expenses' => $totalExpenses,
            'profit' => $profit,
            'average_order_value' => $deliveredOrders->count() > 0 ? $revenue / $deliveredOrders->count() : 0,
            'total_items_sold' => $orders->sum('quantity'),
            'by_status' => $orders->groupBy('status')->map(function ($group) {
                $isDelivered = $group->first()->status === 'delivered';
                return [
                    'count' => $group->count(),
                    'revenue' => $isDelivered ? $group->sum('total_price') : 0,
                    'pending_revenue' => !$isDelivered && $group->first()->status !== 'cancelled' ? $group->sum('total_price') : 0,
                ];
            })->toArray(),
            'by_product' => $orders->groupBy('product_id')->map(function ($group) {
                $product = $group->first()->product;
                $delivered = $group->where('status', 'delivered');
                $pending = $group->whereIn('status', ['pending', 'processing', 'shipped']);
                return [
                    'name' => $product?->name ?? 'Unknown',
                    'count' => $group->count(),
                    'quantity' => $group->sum('quantity'),
                    'revenue' => $delivered->sum('total_price'),
                    'pending_revenue' => $pending->sum('total_price'),
                ];
            })->sortByDesc(fn ($r) => $r['revenue'] + $r['pending_revenue'])->values(),
            'by_product_type' => collect([
                'physical' => [
                    'name' => 'Physical',
                    'orders' => $orders->filter(fn ($o) => $o->product && !$o->product->is_digital),
                ],
                'digital' => [
                    'name' => 'Digital',
                    'orders' => $orders->filter(fn ($o) => $o->product && $o->product->is_digital),
                ],
            ])->map(fn ($data) => [
                'name' => $data['name'],
                'count' => $data['orders']->count(),
                'quantity' => $data['orders']->sum('quantity'),
                'revenue' => $data['orders']->where('status', 'delivered')->sum('total_price'),
                'pending_revenue' => $data['orders']->whereIn('status', ['pending', 'processing', 'shipped'])->sum('total_price'),
            ])->toArray(),
        ];

        return view('admin.reports.sales-print', [
            'orders' => $orders,
            'stats' => $stats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'filters' => [
                'product_id' => $request->input('product_id'),
                'product_type' => $request->input('product_type'),
                'status' => $request->input('status'),
            ],
        ]);
    }

    /**
     * Delete a single order.
     */
    public function destroyOrder(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully!');
    }

    public function restoreOrder(Request $request)
    {
        $order = Order::withTrashed()->findOrFail($request->route('order'));
        $this->authorize('restore', $order);
        $order->restore();
        return redirect()->route('admin.orders.index')->with('success', 'Order restored successfully!');
    }

    public function forceDestroyOrder(Request $request)
    {
        $order = Order::withTrashed()->findOrFail($request->route('order'));
        if (!$order->trashed()) {
            return redirect()->back()->with('error', 'Only soft-deleted orders can be permanently removed.');
        }
        $this->authorize('forceDelete', $order);
        try {
            $order->forceDelete();
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Cannot delete permanently: related records block removal.');
        }

        return redirect()->route('admin.orders.index', ['trashed' => 1])
            ->with('success', 'Order permanently deleted.');
    }

    /**
     * Bulk delete orders by IDs.
     */
    public function bulkDeleteOrders(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer|exists:orders,id',
        ]);

        Order::whereIn('id', $validated['order_ids'])->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Selected orders deleted successfully!');
    }

    /**
     * Bulk mark orders as shipped and auto-create Steadfast parcels.
     */
    public function bulkMarkShipped(Request $request, SteadfastService $steadfast)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer|exists:orders,id',
        ]);

        $orders = Order::with('product')->whereIn('id', $validated['order_ids'])->get();
        $updated = 0;
        $steadfastCreated = 0;

        foreach ($orders as $order) {
            if ($order->status === 'shipped') {
                continue;
            }
            $oldStatus = $order->status;
            $order->status = 'shipped';
            $order->save();
            $updated++;

            OrderLog::create([
                'order_id' => $order->id,
                'admin_id' => Auth::id(),
                'type' => 'status_changed',
                'from_status' => $oldStatus,
                'to_status' => 'shipped',
                'notes' => 'Bulk mark as shipped',
                'meta' => null,
            ]);

            if ($steadfast->isConfigured() && $order->product && !$order->product->is_digital && !$order->steadfast_consignment_id) {
                $result = $steadfast->createOrder($order);
                if ($result['success']) {
                    $steadfastCreated++;
                }
            }
        }

        $msg = $updated ? "{$updated} order(s) marked as shipped." : 'No orders updated.';
        if ($steadfastCreated) {
            $msg .= " Steadfast parcels created: {$steadfastCreated}.";
        }

        return redirect()->route('admin.orders.index', request()->only(['search', 'status', 'product_id', 'product_type', 'category_id', 'date_from', 'date_to', 'per_page']))
            ->with('success', $msg);
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
        
        // Handle home_features as JSON
        if (isset($data['home_features']) && is_array($data['home_features'])) {
            $features = [];
            foreach ($data['home_features'] as $feature) {
                // Skip empty features
                if (empty($feature['title']) && empty($feature['icon'])) {
                    continue;
                }
                $features[] = [
                    'icon' => $feature['icon'] ?? '',
                    'title' => $feature['title'] ?? '',
                    'description' => $feature['description'] ?? '',
                ];
            }
            // Ensure we have exactly 4 features (pad with empty ones if needed)
            while (count($features) < 4) {
                $features[] = ['icon' => '', 'title' => '', 'description' => ''];
            }
            Setting::set('home_features', json_encode(array_slice($features, 0, 4)), 'json', 'home', 'Home page features');
            unset($data['home_features']);
        }
        
        // Handle footer_settings as JSON
        if (isset($data['footer_settings']) && is_array($data['footer_settings'])) {
            $footerSettings = $data['footer_settings'];
            
            // Process columns - clean up empty arrays and ensure proper structure
            if (isset($footerSettings['columns']) && is_array($footerSettings['columns'])) {
                $columns = [];
                foreach ($footerSettings['columns'] as $column) {
                    if (empty($column['title']) && empty($column['content']) && empty($column['links']) && empty($column['items'])) {
                        continue; // Skip completely empty columns
                    }
                    
                    $processedColumn = [
                        'title' => $column['title'] ?? '',
                        'type' => $column['type'] ?? 'text',
                    ];
                    
                    if ($processedColumn['type'] === 'text') {
                        $processedColumn['content'] = $column['content'] ?? '';
                    } elseif ($processedColumn['type'] === 'links') {
                        $processedColumn['links'] = [];
                        if (isset($column['links']) && is_array($column['links'])) {
                            foreach ($column['links'] as $link) {
                                if (!empty($link['text']) || !empty($link['url'])) {
                                    $processedColumn['links'][] = [
                                        'text' => $link['text'] ?? '',
                                        'url' => $link['url'] ?? ''
                                    ];
                                }
                            }
                        }
                    } elseif (in_array($processedColumn['type'], ['service', 'badges'])) {
                        $processedColumn['items'] = [];
                        if (isset($column['items']) && is_array($column['items'])) {
                            foreach ($column['items'] as $item) {
                                if (!empty($item['text']) || !empty($item['icon'])) {
                                    $processedColumn['items'][] = [
                                        'icon' => $item['icon'] ?? '',
                                        'text' => $item['text'] ?? ''
                                    ];
                                }
                            }
                        }
                    }
                    
                    $columns[] = $processedColumn;
                }
                // Ensure we have exactly 4 columns (pad with empty ones if needed)
                while (count($columns) < 4) {
                    $columns[] = ['title' => '', 'type' => 'text', 'content' => ''];
                }
                $footerSettings['columns'] = array_slice($columns, 0, 4);
            }
            
            Setting::set('footer_settings', json_encode($footerSettings), 'json', 'footer', 'Footer settings and content');
            unset($data['footer_settings']);
        }
        
        // Handle checkbox settings (they won't be in request if unchecked)
        Setting::set('order_hide_summary', $request->has('order_hide_summary') ? '1' : '0');
        Setting::set('order_hide_quantity', $request->has('order_hide_quantity') ? '1' : '0');
        unset($data['order_hide_summary'], $data['order_hide_quantity']);

        // Steadfast: don't overwrite secret key if left blank (password-style field)
        if (isset($data['steadfast_secret_key']) && $data['steadfast_secret_key'] === '') {
            unset($data['steadfast_secret_key']);
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
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
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
        
        // Handle photo upload with auto-resize
        if ($request->hasFile('photo')) {
            try {
                $this->resizeAndReplaceUserPhoto($request, $user, $data);
            } catch (\Exception $e) {
                \Log::error('Photo upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['photo' => 'Failed to upload photo. ' . $e->getMessage()]);
            }
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
        if ($user->password && $request->filled('current_password') && !\Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        // Update password (Laravel will auto-hash due to 'hashed' cast in User model)
        $user->update([
            'password' => $request->password,
        ]);
        
        return redirect()->to(route('admin.profile.edit') . '#password')
            ->with('success', 'Password changed successfully!');
    }
}
