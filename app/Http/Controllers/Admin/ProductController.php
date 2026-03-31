<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }
        
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

        // Filter by product type (digital/physical)
        if ($request->filled('is_digital')) {
            $value = $request->is_digital;
            if (in_array($value, ['1', 'true', 'digital'], true)) {
                $query->where('is_digital', true);
            } elseif (in_array($value, ['0', 'false', 'physical'], true)) {
                $query->where('is_digital', false);
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
        // Normalize empty price so nullable validation accepts it
        $request->merge(['price' => $request->input('price') === '' ? null : $request->input('price')]);
        // Ensure payment_options is always an array (empty when no checkboxes checked)
        $request->merge(['payment_options' => $request->input('payment_options', [])]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cashback_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|string|in:fixed,percent',
            'commission_value' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'is_active' => 'boolean',
            'is_digital' => 'boolean',
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
            'payment_options' => 'required|array|min:1',
            'payment_options.*' => 'in:cod,bkash',
            'digital_content_type' => 'nullable|string|in:file,link',
            'digital_file' => 'nullable|file|max:51200', // 50MB
            'digital_link_text' => 'nullable|string|max:10000',
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.compare_at_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|string|max:2048',
            'variants.*.attributes' => 'nullable|string|max:2000',
            'variants.*.sort_order' => 'nullable|integer|min:0',
        ], [
            'payment_options.required' => 'At least one payment method must be selected.',
            'payment_options.min' => 'At least one payment method must be selected.',
        ]);

        $isDigital = filter_var($request->input('is_digital'), FILTER_VALIDATE_BOOLEAN);
        if ($isDigital) {
            $request->validate([
                'digital_content_type' => 'required|in:file,link',
            ], [], ['digital_content_type' => 'digital delivery type']);
            $validated['digital_content_type'] = $request->input('digital_content_type');
            if ($validated['digital_content_type'] === 'file') {
                $request->validate(['digital_file' => 'required|file|max:51200'], [], ['digital_file' => 'digital file']);
                $validated['digital_link_text'] = null;
            }
            if ($validated['digital_content_type'] === 'link') {
                $request->validate(['digital_link_text' => 'required|string|max:10000'], [], ['digital_link_text' => 'link or text']);
                $validated['digital_link_text'] = trim($request->input('digital_link_text'));
                $validated['digital_file_path'] = null;
            }
        } else {
            $validated['digital_content_type'] = null;
            $validated['digital_file_path'] = null;
            $validated['digital_link_text'] = null;
        }
        
        // Sanitize payment_options (unique valid values only)
        $validated['payment_options'] = array_values(array_unique(array_filter($validated['payment_options'], fn($v) => in_array($v, ['cod', 'bkash']))));
        
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['in_stock'] = $isDigital ? true : (($validated['stock_quantity'] ?? 0) > 0);

        // Defaults for earnings settings
        $validated['cashback_amount'] = $validated['cashback_amount'] ?? 0;
        $validated['commission_type'] = $validated['commission_type'] ?? 'fixed';
        $validated['commission_value'] = $validated['commission_value'] ?? 0;
        $validated['only_on_categories'] = $validated['only_on_categories'] ?? false;
        $validated['price'] = $validated['price'] ?? 0;

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
        $validated['is_digital'] = filter_var($request->input('is_digital'), FILTER_VALIDATE_BOOLEAN);

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

        // Remove file from validated (handled after create)
        unset($validated['digital_file']);
        
        $variantsData = $validated['variants'] ?? [];
        unset($validated['variants']);

        $product = DB::transaction(function () use ($validated, $variantsData) {
            $product = Product::create($validated);
            $this->syncVariants($product, $variantsData);
            return $product;
        });

        if ($isDigital && $validated['digital_content_type'] === 'file' && $request->hasFile('digital_file')) {
            $path = $request->file('digital_file')->store('digital-products/' . $product->id, 'public');
            $product->update(['digital_file_path' => $path]);
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }
    
    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->orderBy('name')->get();
        $product->load('variants');
        return view('admin.products.edit', compact('product', 'categories'));
    }
    
    public function builder(Product $product)
    {
        return view('admin.products.builder', compact('product'));
    }
    
    public function update(Request $request, Product $product)
    {
        // Normalize empty price so nullable validation accepts it
        $request->merge(['price' => $request->input('price') === '' ? null : $request->input('price')]);
        // Ensure payment_options is always an array (empty when no checkboxes checked)
        $request->merge(['payment_options' => $request->input('payment_options', [])]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cashback_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|string|in:fixed,percent',
            'commission_value' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'page_layout' => 'nullable',
            'is_active' => 'boolean',
            'is_digital' => 'boolean',
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
            'payment_options' => 'required|array|min:1',
            'payment_options.*' => 'in:cod,bkash',
            'digital_content_type' => 'nullable|string|in:file,link',
            'digital_file' => 'nullable|file|max:51200',
            'digital_link_text' => 'nullable|string|max:10000',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.compare_at_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|string|max:2048',
            'variants.*.attributes' => 'nullable|string|max:2000',
            'variants.*.sort_order' => 'nullable|integer|min:0',
        ], [
            'payment_options.required' => 'At least one payment method must be selected.',
            'payment_options.min' => 'At least one payment method must be selected.',
        ]);

        $isDigital = filter_var($request->input('is_digital'), FILTER_VALIDATE_BOOLEAN);
        if ($isDigital) {
            $request->validate([
                'digital_content_type' => 'required|in:file,link',
            ], [], ['digital_content_type' => 'digital delivery type']);
            $validated['digital_content_type'] = $request->input('digital_content_type');
            if ($validated['digital_content_type'] === 'file') {
                $request->validate(['digital_file' => 'nullable|file|max:51200'], [], ['digital_file' => 'digital file']);
                if (!$product->digital_file_path && !$request->hasFile('digital_file')) {
                    $request->validate(['digital_file' => 'required'], ['digital_file.required' => 'Please upload a file or switch to link/text.']);
                }
            }
            if ($validated['digital_content_type'] === 'file') {
                $validated['digital_link_text'] = null;
            }
            if ($validated['digital_content_type'] === 'link') {
                $request->validate(['digital_link_text' => 'required|string|max:10000'], [], ['digital_link_text' => 'link or text']);
                $validated['digital_link_text'] = trim($request->input('digital_link_text'));
                $validated['digital_file_path'] = null;
            }
        } else {
            $validated['digital_content_type'] = null;
            $validated['digital_file_path'] = null;
            $validated['digital_link_text'] = null;
        }
        
        // Sanitize payment_options (unique valid values only)
        $validated['payment_options'] = array_values(array_unique(array_filter($validated['payment_options'], fn($v) => in_array($v, ['cod', 'bkash']))));
        
        // Handle page_layout - it comes as JSON string from the form
        // Only update page_layout if it's provided in the request
        if (array_key_exists('page_layout', $validated)) {
            if (isset($validated['page_layout']) && is_string($validated['page_layout']) && $validated['page_layout'] !== '') {
                $decoded = json_decode($validated['page_layout'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $validated['page_layout'] = $decoded;
                } else {
                    // Invalid JSON: preserve existing layout
                    unset($validated['page_layout']);
                }
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
        
        $validated['in_stock'] = $isDigital ? true : (($validated['stock_quantity'] ?? $product->stock_quantity ?? 0) > 0);

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
        $validated['price'] = $validated['price'] ?? 0;

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
        
        // When saving from page builder, do not overwrite order form settings (keep existing product values)
        $isLayoutOnlySave = $request->filled('page_layout');

        if (!$isLayoutOnlySave) {
            // Handle order form settings only when editing product (not from builder)
            $validated['order_hide_summary'] = $request->has('order_hide_summary');
            $validated['order_hide_quantity'] = $request->has('order_hide_quantity');
            $validated['is_free'] = $request->has('is_free');
            $validated['is_digital'] = filter_var($request->input('is_digital'), FILTER_VALIDATE_BOOLEAN);
        } else {
            // Remove order-form and other fields so update() does not touch them (preserve existing)
            $preserveKeys = [
                'order_form_title', 'order_button_text', 'order_min_quantity', 'order_max_quantity',
                'order_delivery_options', 'order_hide_summary', 'order_hide_quantity', 'is_free',
                'payment_options', 'sms_templates', 'order_custom_charge', 'is_digital',
                'digital_content_type', 'digital_file_path', 'digital_link_text',
            ];
            foreach ($preserveKeys as $key) {
                unset($validated[$key]);
            }
        }

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

        unset($validated['digital_file']);
        $variantsData = $validated['variants'] ?? [];
        unset($validated['variants']);

        DB::transaction(function () use ($product, $validated, $variantsData) {
            $product->update($validated);
            $this->syncVariants($product, $variantsData);
        });

        if ($isDigital && $validated['digital_content_type'] === 'file' && $request->hasFile('digital_file')) {
            if ($product->digital_file_path && Storage::disk('public')->exists($product->digital_file_path)) {
                Storage::disk('public')->delete($product->digital_file_path);
            }
            $path = $request->file('digital_file')->store('digital-products/' . $product->id, 'public');
            $product->update(['digital_file_path' => $path]);
        }
        
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

    public function restore(Request $request)
    {
        $product = Product::withTrashed()->findOrFail($request->route('product'));
        $this->authorize('restore', $product);
        $product->restore();
        return redirect()->route('admin.products.index')->with('success', 'Product restored successfully!');
    }

    protected function syncVariants(Product $product, array $variantsData): void
    {
        $existingVariants = ProductVariant::withTrashed()
            ->where('product_id', $product->id)
            ->get()
            ->keyBy('id');

        $keptIds = [];

        foreach ($variantsData as $row) {
            $name = trim((string)($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $attributes = null;
            if (!empty($row['attributes'])) {
                $decoded = json_decode($row['attributes'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $attributes = $decoded;
                }
            }

            $payload = [
                'name' => $name,
                'sku' => !empty($row['sku']) ? trim((string)$row['sku']) : null,
                'price' => isset($row['price']) && $row['price'] !== '' ? (float)$row['price'] : 0,
                'compare_at_price' => isset($row['compare_at_price']) && $row['compare_at_price'] !== '' ? (float)$row['compare_at_price'] : null,
                'stock_quantity' => isset($row['stock_quantity']) && $row['stock_quantity'] !== '' ? (int)$row['stock_quantity'] : 0,
                'in_stock' => isset($row['in_stock']) ? (bool)$row['in_stock'] : ((int)($row['stock_quantity'] ?? 0) > 0),
                'image' => !empty($row['image']) ? trim((string)$row['image']) : null,
                'attributes' => $attributes,
                'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
                'sort_order' => isset($row['sort_order']) && $row['sort_order'] !== '' ? (int)$row['sort_order'] : 0,
            ];

            $variantId = isset($row['id']) && $row['id'] !== '' ? (int)$row['id'] : null;
            if ($variantId && $existingVariants->has($variantId)) {
                $variant = $existingVariants->get($variantId);
                if ($variant->trashed()) {
                    $variant->restore();
                }
                $variant->update($payload);
                $keptIds[] = $variant->id;
                continue;
            }

            $newVariant = $product->variants()->create($payload);
            $keptIds[] = $newVariant->id;
        }

        ProductVariant::where('product_id', $product->id)
            ->when(!empty($keptIds), fn ($q) => $q->whereNotIn('id', $keptIds))
            ->delete();
    }
}
