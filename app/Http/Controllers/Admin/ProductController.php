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
            'sku' => 'nullable|string|unique:products,sku',
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);
        
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['in_stock'] = $validated['stock_quantity'] > 0;
        
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
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'page_layout' => 'nullable',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);
        
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
