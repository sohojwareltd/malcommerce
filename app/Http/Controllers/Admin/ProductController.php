<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Get per page value from request, default to 20
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;
        
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        
        $categories = Category::orderBy('name')->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }
    
    public function create()
    {
        $categories = Category::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cashback_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|string|in:fixed,percent',
            'commission_value' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'is_active' => 'boolean',
            'only_on_categories' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'order_form_title' => 'nullable|string|max:255',
            'order_button_text' => 'nullable|string|max:255',
            'order_min_quantity' => 'nullable|integer|min:0',
            'order_max_quantity' => 'nullable|integer|min:0',
            'order_delivery_options' => 'nullable|string',
            'order_hide_summary' => 'boolean',
            'order_hide_quantity' => 'boolean',
            'is_free' => 'boolean',
            'sms_templates' => 'nullable|array',
            'sms_templates.*' => 'nullable|string|max:500',
            'payment_options' => 'nullable|array',
            'payment_options.*' => 'in:cod,bkash',
        ]);
        
        // Handle payment_options - if empty array, set to null (allows all methods)
        if (isset($validated['payment_options']) && is_array($validated['payment_options'])) {
            if (empty($validated['payment_options'])) {
                $validated['payment_options'] = null;
            } else {
                // Ensure unique values and valid options
                $validated['payment_options'] = array_unique(array_filter($validated['payment_options'], fn($v) => in_array($v, ['cod', 'bkash'])));
                if (empty($validated['payment_options'])) {
                    $validated['payment_options'] = null;
                }
            }
        }
        
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['in_stock'] = $validated['stock_quantity'] > 0;

        // Defaults for earnings settings
        $validated['cashback_amount'] = $validated['cashback_amount'] ?? 0;
        $validated['commission_type'] = $validated['commission_type'] ?? 'fixed';
        $validated['commission_value'] = $validated['commission_value'] ?? 0;
        $validated['only_on_categories'] = $validated['only_on_categories'] ?? false;
        
        // Handle images - filter out empty values
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = array_filter($validated['images'], fn($img) => !empty($img));
            $validated['images'] = array_values($validated['images']); // Re-index array
            if (empty($validated['images'])) {
                $validated['images'] = null;
            }
        } else {
            $validated['images'] = null;
        }
        
        // Handle order form settings
        $validated['order_hide_summary'] = $request->has('order_hide_summary');
        $validated['order_hide_quantity'] = $request->has('order_hide_quantity');
        $validated['is_free'] = $request->has('is_free');

        // Clean SMS templates (remove empty ones)
        if (isset($validated['sms_templates']) && is_array($validated['sms_templates'])) {
            $validated['sms_templates'] = array_filter(
                array_map(fn($value) => is_string($value) ? trim($value) : '', $validated['sms_templates']),
                fn($value) => $value !== ''
            );
            if (empty($validated['sms_templates'])) {
                $validated['sms_templates'] = null;
            }
        }
        
        // If product is free, set price to 0
        if ($validated['is_free'] ?? false) {
            $validated['price'] = 0;
        }
        
        if (isset($validated['order_delivery_options']) && is_string($validated['order_delivery_options'])) {
            // Validate JSON
            $decoded = json_decode($validated['order_delivery_options'], true);
            $validated['order_delivery_options'] = $decoded ? json_encode($decoded) : null;
        }
        
        Product::create($validated);
        
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }
    
    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }
    
    public function builder(Product $product)
    {
        return view('admin.products.builder', compact('product'));
    }
    
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cashback_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|string|in:fixed,percent',
            'commission_value' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'page_layout' => 'nullable',
            'is_active' => 'boolean',
            'only_on_categories' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'order_form_title' => 'nullable|string|max:255',
            'order_button_text' => 'nullable|string|max:255',
            'order_min_quantity' => 'nullable|integer|min:0',
            'order_max_quantity' => 'nullable|integer|min:0',
            'order_delivery_options' => 'nullable|string',
            'order_hide_summary' => 'boolean',
            'order_hide_quantity' => 'boolean',
            'is_free' => 'boolean',
            'sms_templates' => 'nullable|array',
            'sms_templates.*' => 'nullable|string|max:500',
            'payment_options' => 'nullable|array',
            'payment_options.*' => 'in:cod,bkash',
        ]);
        
        // Handle payment_options - if empty array, set to null (allows all methods)
        if (isset($validated['payment_options']) && is_array($validated['payment_options'])) {
            if (empty($validated['payment_options'])) {
                $validated['payment_options'] = null;
            } else {
                // Ensure unique values and valid options
                $validated['payment_options'] = array_unique(array_filter($validated['payment_options'], fn($v) => in_array($v, ['cod', 'bkash'])));
                if (empty($validated['payment_options'])) {
                    $validated['payment_options'] = null;
                }
            }
        }
        
        // Handle page_layout - it comes as JSON string from the form
        // Only update page_layout if it's provided in the request
        if (array_key_exists('page_layout', $validated)) {
            if (isset($validated['page_layout']) && is_string($validated['page_layout'])) {
                $decoded = json_decode($validated['page_layout'], true);
                $validated['page_layout'] = $decoded ?: null;
            } elseif (!isset($validated['page_layout']) || $validated['page_layout'] === '') {
                $validated['page_layout'] = null;
            }
        } else {
            // Preserve existing page_layout if not in request
            unset($validated['page_layout']);
        }
        
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['in_stock'] = $validated['stock_quantity'] > 0;
        
        // Defaults for earnings settings (if not provided, keep existing values)
        if (!isset($validated['cashback_amount'])) {
            $validated['cashback_amount'] = $product->cashback_amount ?? 0;
        }
        if (!isset($validated['only_on_categories'])) {
            $validated['only_on_categories'] = $product->only_on_categories ?? false;
        }
        if (!isset($validated['commission_type'])) {
            $validated['commission_type'] = $product->commission_type ?? 'fixed';
        }
        if (!isset($validated['commission_value'])) {
            $validated['commission_value'] = $product->commission_value ?? 0;
        }
        
        // Handle images - only update if provided in request
        if (array_key_exists('images', $validated)) {
            if (isset($validated['images']) && is_array($validated['images'])) {
                $validated['images'] = array_filter($validated['images'], fn($img) => !empty($img));
                $validated['images'] = array_values($validated['images']); // Re-index array
                if (empty($validated['images'])) {
                    $validated['images'] = null;
                }
            } else {
                $validated['images'] = null;
            }
        } else {
            // Preserve existing images if not in request
            unset($validated['images']);
        }
        
        // Handle order form settings
        $validated['order_hide_summary'] = $request->has('order_hide_summary');
        $validated['order_hide_quantity'] = $request->has('order_hide_quantity');
        $validated['is_free'] = $request->has('is_free');

        // Clean SMS templates (remove empty ones)
        if (array_key_exists('sms_templates', $validated)) {
            if (isset($validated['sms_templates']) && is_array($validated['sms_templates'])) {
                $validated['sms_templates'] = array_filter(
                    array_map(fn($value) => is_string($value) ? trim($value) : '', $validated['sms_templates']),
                    fn($value) => $value !== ''
                );
                if (empty($validated['sms_templates'])) {
                    $validated['sms_templates'] = null;
                }
            } else {
                $validated['sms_templates'] = null;
            }
        }
        
        // If product is free, set price to 0
        if ($validated['is_free'] ?? false) {
            $validated['price'] = 0;
        }
        
        if (isset($validated['order_delivery_options']) && is_string($validated['order_delivery_options'])) {
            // Validate JSON
            $decoded = json_decode($validated['order_delivery_options'], true);
            $validated['order_delivery_options'] = $decoded ? json_encode($decoded) : null;
        }
        
        $product->update($validated);
        
        // If coming from builder, redirect back to builder
        if ($request->has('page_layout')) {
            return redirect()->route('admin.products.builder', $product)->with('success', 'Layout saved successfully!');
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }
    
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
