@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Edit Product</h1>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6" onsubmit="updateDeliveryOptionsJson(); return true;">
    @csrf
    @method('PUT')

    @php
        $smsTemplates = old('sms_templates', $product->sms_templates ?? []);
        $orderStatuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        $existingVariantsForJs = ($product->variants ?? collect())->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'compare_at_price' => $variant->compare_at_price,
                'stock_quantity' => $variant->stock_quantity,
                'in_stock' => (bool) $variant->in_stock,
                'is_active' => (bool) $variant->is_active,
                'image' => $variant->image,
                'attributes' => $variant->attributes,
                'sort_order' => $variant->sort_order,
            ];
        })->values()->toArray();
    @endphp

    <div class="border-b border-neutral-200 mb-6">
        <div class="inline-flex rounded-lg border border-neutral-200 overflow-hidden">
            <button type="button" data-tab-target="details" class="tab-button px-4 py-2 text-sm font-semibold text-primary bg-primary/10">
                Product Details
            </button>
            <button type="button" data-tab-target="order" class="tab-button px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100">
                Order Form
            </button>
            <button type="button" data-tab-target="variations" class="tab-button px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100">
                Variations
            </button>
            <button type="button" data-tab-target="sms" class="tab-button px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100">
                SMS Settings
            </button>
        </div>
    </div>
    
    <div id="tab-details" class="tab-panel">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Name *</label>
            <input type="text" name="name" id="product-name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
            <input type="text" name="slug" id="product-slug" value="{{ old('slug', $product->slug) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            <p class="mt-1 text-xs text-neutral-500">Auto-generated from name</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Category</label>
            <div class="flex gap-2">
                <select name="category_id" id="category_id" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="openQuickCreateCategoryModal()" class="px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg hover:bg-neutral-200 transition font-medium">
                    + Quick Add
                </button>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Product Type</label>
            @php $isDigital = old('is_digital', $product->is_digital ?? false); @endphp
            <select name="is_digital" id="is_digital" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                <option value="0" {{ !$isDigital ? 'selected' : '' }}>Physical</option>
                <option value="1" {{ $isDigital ? 'selected' : '' }}>Digital</option>
            </select>
            <p class="mt-1 text-xs text-neutral-500">Physical products require shipping. Digital products are delivered electronically.</p>
        </div>

        @php $digitalType = old('digital_content_type', $product->digital_content_type ?? 'link'); @endphp
        <div id="digital-content-section" class="md:col-span-2 {{ $isDigital ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-neutral-700 mb-2">Digital Delivery</label>
            <p class="text-xs text-neutral-500 mb-3">Choose how customers receive this digital product after purchase.</p>
            <div class="space-y-3">
                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="digital_content_type" value="file" {{ $digitalType === 'file' ? 'checked' : '' }} class="text-primary border-neutral-300 focus:ring-primary">
                        <span class="text-sm">Upload file</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="digital_content_type" value="link" {{ $digitalType === 'link' ? 'checked' : '' }} class="text-primary border-neutral-300 focus:ring-primary">
                        <span class="text-sm">Link or text (playlist, Drive, etc.)</span>
                    </label>
                </div>
                <div id="digital-file-wrap" class="{{ $digitalType === 'file' ? '' : 'hidden' }}">
                    @if($product->digital_file_path)
                    <p class="text-sm text-neutral-600 mb-2">Current file: <span class="font-medium">{{ basename($product->digital_file_path) }}</span></p>
                    @endif
                    <input type="file" name="digital_file" accept=".pdf,.zip,.mp3,.mp4,.doc,.docx,.epub,.m4a,.wav" class="w-full text-sm text-neutral-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-medium">
                    <p class="text-xs text-neutral-500 mt-1">PDF, ZIP, MP3, MP4, DOC, DOCX, EPUB, M4A, WAV. Max 50MB. Leave empty to keep current file.</p>
                    @error('digital_file')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div id="digital-link-wrap" class="{{ $digitalType === 'link' ? '' : 'hidden' }}">
                    <textarea name="digital_link_text" rows="4" placeholder="Paste playlist link, Google Drive link, or any text to share with the customer..." class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('digital_link_text', $product->digital_link_text) }}</textarea>
                    @error('digital_link_text')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Price *</label>
            <div class="flex items-center gap-3">
                <input type="number" name="price" id="product-price" step="0.01" value="{{ old('price', $product->price) }}" required 
                       class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary" 
                       {{ old('is_free', $product->is_free) ? 'disabled' : '' }}>
                <label class="flex items-center gap-2 whitespace-nowrap">
                    <input type="checkbox" name="is_free" id="product-is-free" value="1" 
                           {{ old('is_free', $product->is_free) ? 'checked' : '' }} 
                           onchange="toggleFreeProduct(this)"
                           class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                    <span class="text-sm text-neutral-700">Free Product</span>
                </label>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Compare At Price</label>
            <input type="number" name="compare_at_price" step="0.01" value="{{ old('compare_at_price', $product->compare_at_price) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Stock Quantity *</label>
            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>

    </div>

    <!-- Earning Settings -->
    <div class="mt-6 border-t border-neutral-200 pt-6">
        <h2 class="text-xl font-bold mb-4">Earning Settings</h2>
        <p class="text-sm text-neutral-600 mb-4">Configure customer cashback and sponsor commission for this product.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Cashback Amount (৳)</label>
                <input type="number" name="cashback_amount" step="0.01" min="0" value="{{ old('cashback_amount', $product->cashback_amount) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">Amount given back to the customer per order.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Commission Type</label>
                <select name="commission_type" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    @php $commissionType = old('commission_type', $product->commission_type ?? 'fixed'); @endphp
                    <option value="fixed" {{ $commissionType === 'fixed' ? 'selected' : '' }}>Fixed (৳)</option>
                    <option value="percent" {{ $commissionType === 'percent' ? 'selected' : '' }}>Percent (%)</option>
                </select>
                <p class="text-xs text-neutral-500 mt-1">How sponsor commission is calculated.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Commission Value</label>
                <input type="number" name="commission_value" step="0.01" min="0" value="{{ old('commission_value', $product->commission_value) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">If fixed: amount in ৳. If percent: percentage of order total.</p>
            </div>
        </div>
    </div>
    
    <div class="mt-6">
        <label class="block text-sm font-medium text-neutral-700 mb-2">Product Images</label>
        <div id="images-container" class="space-y-3">
            <!-- Images will be added here dynamically -->
        </div>
        <button type="button" onclick="addImageUpload()" class="mt-3 px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg hover:bg-neutral-200 transition text-sm font-medium">
            + Add Image
        </button>
        @error('images')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
    
    <div class="mt-6">
        <label class="block text-sm font-medium text-neutral-700 mb-2">Short Description</label>
        <textarea name="short_description" rows="2" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('short_description', $product->short_description) }}</textarea>
    </div>
    
    <div class="mt-6">
        <label class="block text-sm font-medium text-neutral-700 mb-2">Description</label>
        <textarea name="description" rows="5" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('description', $product->description) }}</textarea>
    </div>
    
    <div class="mt-6 flex gap-4">
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-neutral-300 text-primary focus:ring-primary">
            <span class="ml-2 text-sm text-neutral-700">Active</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" name="only_on_categories" value="1" {{ old('only_on_categories', $product->only_on_categories) ? 'checked' : '' }} class="rounded border-neutral-300 text-primary focus:ring-primary">
            <span class="ml-2 text-sm text-neutral-700">Only on Categories</span>
        </label>    
        <label class="flex items-center">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-neutral-300 text-primary focus:ring-primary">
            <span class="ml-2 text-sm text-neutral-700">Featured</span>
        </label>
    </div>
    
    </div> <!-- End tab-details -->

    <div id="tab-variations" class="tab-panel hidden">
        <div class="space-y-6">
            <div class="border border-neutral-200 rounded-lg p-4 bg-white">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-base font-semibold text-neutral-900">Attributes (in advance)</h3>
                        <p class="text-xs text-neutral-500 mt-1">Define groups like color/text/image, then generate variant rows automatically.</p>
                    </div>
                    <button type="button" onclick="addAttributeGroup()" class="px-3 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition text-sm font-medium">
                        + Add Attribute Group
                    </button>
                </div>
                <div id="attribute-groups-container" class="space-y-3"></div>
                <div class="mt-4">
                    <button type="button" onclick="generateVariantsFromAttributes()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition text-sm font-semibold">
                        Generate Variants
                    </button>
                </div>
            </div>

        <div class="md:col-span-2 border border-neutral-200 rounded-lg p-4 bg-neutral-50/40">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-base font-semibold text-neutral-900">Product Variations</h3>
                    <p class="text-xs text-neutral-500">Add size/color/pack variations. Leave empty if this product has no variants.</p>
                </div>
                <button type="button" onclick="addVariantRow()" class="px-3 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition text-sm font-medium">
                    + Add Variation
                </button>
            </div>
            <div id="variants-container" class="space-y-3"></div>
            @error('variants')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
        </div>
        </div>
    </div>

    <div id="tab-order" class="tab-panel hidden">
        <!-- Order Form Settings -->
        <div class="mt-2">
            <h2 class="text-xl font-bold mb-4">Order Form Settings</h2>
            <p class="text-sm text-neutral-600 mb-4">Customize the order form for this product. Leave empty to use global settings.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Order Form Title</label>
                    <input type="text" name="order_form_title" value="{{ old('order_form_title', $product->order_form_title) }}" placeholder="Leave empty for default" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-neutral-500 mt-1">Default: "অর্ডার করুন"</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Order Button Text</label>
                    <input type="text" name="order_button_text" value="{{ old('order_button_text', $product->order_button_text) }}" placeholder="Leave empty for default" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-neutral-500 mt-1">Default: "অর্ডার নিশ্চিত করুন"</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Minimum Order Quantity</label>
                    <input type="number" name="order_min_quantity" value="{{ old('order_min_quantity', $product->order_min_quantity) }}" min="0" step="1" placeholder="0 = no minimum" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-neutral-500 mt-1">Minimum number of items required per order</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Maximum Order Quantity</label>
                    <input type="number" name="order_max_quantity" value="{{ old('order_max_quantity', $product->order_max_quantity) }}" min="0" step="1" placeholder="0 = no maximum" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-neutral-500 mt-1">Maximum number of items allowed per order</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Delivery Options</label>
                    <p class="text-xs text-neutral-500 mb-3">Leave empty to use global settings. Click "Add Delivery Option" to add a new row.</p>
                    
                    <div id="delivery-options-container" class="space-y-3 mb-3">
                        <!-- Delivery options rows will be added here dynamically -->
                    </div>
                    
                    <button type="button" onclick="addDeliveryOption()" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition text-sm font-medium">
                        + Add Delivery Option
                    </button>
                    
                    <!-- Hidden input to store JSON -->
                    <input type="hidden" name="order_delivery_options" id="order_delivery_options_json" value="{{ old('order_delivery_options', $product->order_delivery_options ?: '') }}">
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="order_hide_summary" value="1" {{ old('order_hide_summary', $product->order_hide_summary) ? 'checked' : '' }} class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                    <span class="text-sm font-medium text-neutral-700">Hide Order Summary</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="order_hide_quantity" value="1" {{ old('order_hide_quantity', $product->order_hide_quantity) ? 'checked' : '' }} class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                    <span class="text-sm font-medium text-neutral-700">Hide Quantity Selector</span>
                </label>
            </div>
        </div>
        
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-neutral-800 mb-3">Payment Options</h3>
            <p class="text-sm text-neutral-600 mb-4">Select which payment methods are available for this product. At least one must be selected.</p>
            <div class="space-y-3">
                @php
                    $paymentOptions = old('payment_options', $product->payment_options ?? ['cod']);
                    if (!is_array($paymentOptions)) {
                        $paymentOptions = [];
                    }
                @endphp
                <label class="flex items-center gap-3 p-4 border border-neutral-300 rounded-lg hover:bg-neutral-50 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="payment_options[]" 
                        value="cod"
                        {{ in_array('cod', $paymentOptions) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary"
                    >
                    <div class="flex-1">
                        <div class="font-semibold text-neutral-900 flex items-center gap-2">
                            <span>💵</span>
                            <span>Cash on Delivery (COD)</span>
                        </div>
                        <div class="text-sm text-neutral-600 mt-1">
                            Customer pays when the product is delivered
                        </div>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 border border-neutral-300 rounded-lg hover:bg-neutral-50 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="payment_options[]" 
                        value="bkash"
                        {{ in_array('bkash', $paymentOptions) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary"
                    >
                    <div class="flex-1">
                        <div class="font-semibold text-neutral-900 flex items-center gap-2">
                            <span>📱</span>
                            <span>bKash</span>
                        </div>
                        <div class="text-sm text-neutral-600 mt-1">
                            Online payment via bKash gateway
                        </div>
                    </div>
                </label>
            </div>
            <p class="text-xs text-neutral-500 mt-3">
                <strong>Note:</strong> At least one payment method must be selected. Cash on Delivery (COD) is the default.
            </p>
        </div>
    </div> <!-- End tab-order -->

    <div id="tab-sms" class="tab-panel hidden">
        <div class="mt-2">
            <h2 class="text-xl font-bold mb-4">SMS Settings</h2>
            <p class="text-sm text-neutral-600 mb-4">Set custom SMS templates for each order status. Leave blank to use the default message.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($orderStatuses as $statusKey => $statusLabel)
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-semibold text-neutral-800">{{ $statusLabel }} Message</label>
                        <span class="text-xs text-neutral-500 uppercase tracking-wide">{{ $statusKey }}</span>
                    </div>
                    <textarea name="sms_templates[{{ $statusKey }}]" rows="4" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="e.g., Order #{order_number} is now {{ strtolower($statusLabel) }}.">{{ $smsTemplates[$statusKey] ?? '' }}</textarea>
                    <p class="text-xs text-neutral-500">Placeholders: {order_number}, {customer_name}, {product_name}, {status}, {quantity}, {total_price}, {delivery_charge}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div> <!-- End tab-sms -->
    
    <div class="mt-6">
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
            Update Product
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-4 text-neutral-700 hover:text-neutral-900">Cancel</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');

    function activateTab(target) {
        tabPanels.forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== `tab-${target}`);
        });

        tabButtons.forEach(button => {
            const isActive = button.dataset.tabTarget === target;
            button.classList.toggle('bg-primary/10', isActive);
            button.classList.toggle('text-primary', isActive);
            button.classList.toggle('text-neutral-700', !isActive);
        });
    }

    tabButtons.forEach(button => {
        button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
    });

    activateTab('details');
});

let imageIndex = 0;
let variantIndex = 0;
let attributeGroupIndex = 0;

function addAttributeGroup() {
    const container = document.getElementById('attribute-groups-container');
    const index = attributeGroupIndex++;

    const groupCard = document.createElement('div');
    groupCard.className = 'attr-group-card p-3 sm:p-4 border border-neutral-200 rounded-lg bg-white';
    groupCard.dataset.attrGroupIndex = index;
    groupCard.innerHTML = `
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex-1">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Group Name</label>
                <input type="text" class="attr-group-name w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm" placeholder="e.g. Color">
            </div>
            <button type="button" onclick="removeAttributeGroup(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm whitespace-nowrap">
                Remove
            </button>
        </div>

        <div class="flex flex-wrap gap-3 items-end mb-3">
            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Value Type</label>
                <select class="attr-group-type w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm" onchange="syncAttributeGroupValueVisibility(${index})">
                    <option value="text">Text</option>
                    <option value="color">Color</option>
                    <option value="image">Image</option>
                </select>
            </div>
            <div>
                <button type="button" onclick="addAttributeValueRow(${index})" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition text-sm font-medium">
                    + Add Value
                </button>
            </div>
        </div>

        <div class="space-y-3 attr-values-container"></div>
    `;

    container.appendChild(groupCard);
}

function removeAttributeGroup(button) {
    button.closest('.attr-group-card').remove();
}

function addAttributeValueRow(groupIndex) {
    const groupCard = document.querySelector(`.attr-group-card[data-attr-group-index="${groupIndex}"]`);
    if (!groupCard) return;

    const valuesContainer = groupCard.querySelector('.attr-values-container');
    const type = groupCard.querySelector('.attr-group-type').value;

    const valueRow = document.createElement('div');
    valueRow.className = 'attr-value-row p-3 border border-neutral-200 rounded-lg bg-neutral-50/40';
    valueRow.innerHTML = `
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Label</label>
                <input type="text" class="attr-value-label w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm" placeholder="e.g. Red">
            </div>

            <div class="attr-value-color-wrap ${type === 'color' ? '' : 'hidden'}">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" class="attr-value-color w-10 h-10 rounded border border-neutral-300" value="#000000">
                    <input type="text" class="attr-value-color-hex w-28 px-3 py-2 border border-neutral-300 rounded-lg text-sm" placeholder="#RRGGBB" value="#000000">
                </div>
            </div>

            <div class="attr-value-image-wrap ${type === 'image' ? '' : 'hidden'}">
                <label class="block text-xs font-medium text-neutral-700 mb-1">Image</label>
                <div class="flex items-center gap-2">
                    <div class="attr-value-image-preview w-10 h-10 rounded border border-neutral-200 overflow-hidden" style="background:#f3f4f6;">
                        <img class="w-full h-full object-cover hidden" alt="Attribute value image">
                    </div>
                    <input type="hidden" class="attr-value-image-url">
                    <button type="button" onclick="uploadAttributeValueImage(this)" class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition text-xs">
                        Upload
                    </button>
                </div>
            </div>

            <button type="button" onclick="removeAttributeValueRow(this)" class="px-3 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition text-xs whitespace-nowrap">
                Remove
            </button>
        </div>
    `;

    valuesContainer.appendChild(valueRow);

    const colorPicker = valueRow.querySelector('.attr-value-color');
    const hexInput = valueRow.querySelector('.attr-value-color-hex');
    if (colorPicker && hexInput) {
        colorPicker.addEventListener('input', () => {
            hexInput.value = colorPicker.value;
        });
    }
}

function removeAttributeValueRow(button) {
    button.closest('.attr-value-row').remove();
}

function syncAttributeGroupValueVisibility(groupIndex) {
    const groupCard = document.querySelector(`.attr-group-card[data-attr-group-index="${groupIndex}"]`);
    if (!groupCard) return;

    const type = groupCard.querySelector('.attr-group-type').value;
    groupCard.querySelectorAll('.attr-value-color-wrap').forEach(el => el.classList.toggle('hidden', type !== 'color'));
    groupCard.querySelectorAll('.attr-value-image-wrap').forEach(el => el.classList.toggle('hidden', type !== 'image'));
}

async function uploadAttributeValueImage(button) {
    const valueRow = button.closest('.attr-value-row');
    const hiddenInput = valueRow.querySelector('.attr-value-image-url');
    const previewWrap = valueRow.querySelector('.attr-value-image-preview');
    const previewImg = previewWrap.querySelector('img');

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'products');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = 'Uploading...';

        try {
            const response = await fetch('{{ route("admin.upload.image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();
            if (!data.success || !data.url) {
                alert('Failed to upload image');
                return;
            }

            hiddenInput.value = data.url;
            previewImg.src = data.url;
            previewImg.classList.remove('hidden');
            button.textContent = 'Change';
        } catch (err) {
            console.error('Attribute value image upload failed:', err);
            alert('Error uploading image');
        } finally {
            button.disabled = false;
            if (button.textContent === 'Uploading...') button.textContent = originalText;
        }
    };

    input.click();
}

function canonicalizeAttributes(attributes) {
    const out = {};
    Object.keys(attributes || {}).sort().forEach((k) => {
        out[k] = attributes[k];
    });
    return JSON.stringify(out);
}

function getDefaultVariantDefaults() {
    const priceInput = document.getElementById('product-price');
    const compareInput = document.querySelector('input[name="compare_at_price"]');
    const stockInput = document.querySelector('input[name="stock_quantity"]');
    const isDigital = (document.getElementById('is_digital')?.value || '0') === '1';
    const isFree = !!document.getElementById('product-is-free')?.checked;

    let price = priceInput ? parseFloat(priceInput.value) : 0;
    if (!Number.isFinite(price)) price = 0;
    if (isFree) price = 0;

    let compareAt = compareInput ? parseFloat(compareInput.value) : NaN;
    if (!Number.isFinite(compareAt) || compareAt < 0) compareAt = null;

    let stock = stockInput ? parseInt(stockInput.value, 10) : 0;
    if (!Number.isFinite(stock) || stock < 0) stock = 0;

    const inStock = isDigital ? true : stock > 0;

    return {
        price,
        compareAt,
        stock,
        inStock,
    };
}

function generateVariantsFromAttributes() {
    const groupsContainer = document.getElementById('attribute-groups-container');
    if (!groupsContainer) return;

    const groupCards = groupsContainer.querySelectorAll('.attr-group-card');
    const groups = [];

    groupCards.forEach((card) => {
        const groupName = (card.querySelector('.attr-group-name')?.value || '').trim();
        const type = card.querySelector('.attr-group-type')?.value || 'text';

        const items = [];
        card.querySelectorAll('.attr-value-row').forEach((valueRow) => {
            const label = (valueRow.querySelector('.attr-value-label')?.value || '').trim();
            if (!label) return;

            const item = { label };
            if (type === 'color') {
                const hex = (valueRow.querySelector('.attr-value-color-hex')?.value || '').trim();
                item.label = label || hex || label;
            }
            if (type === 'image') {
                const imageUrl = (valueRow.querySelector('.attr-value-image-url')?.value || '').trim();
                item.imageUrl = imageUrl || null;
            }

            items.push(item);
        });

        if (groupName && items.length > 0) {
            groups.push({ groupName, type, items });
        }
    });

    if (groups.length === 0) {
        alert('Add at least one attribute group with values.');
        return;
    }

    const existingSet = new Set();
    document.querySelectorAll('.variant-attributes-json').forEach((input) => {
        const raw = (input.value || '').trim();
        if (!raw) return;
        try {
            const parsed = JSON.parse(raw);
            existingSet.add(canonicalizeAttributes(parsed));
        } catch (e) {
            // ignore invalid entries
        }
    });

    const defaults = getDefaultVariantDefaults();

    let combinations = [{ attributes: {}, image: null, nameParts: [] }];
    groups.forEach((group) => {
        const next = [];
        combinations.forEach((comb) => {
            group.items.forEach((item) => {
                const attrs = { ...comb.attributes, [group.groupName]: item.label };
                const image = comb.image || (group.type === 'image' ? (item.imageUrl || null) : null);
                const nameParts = [...comb.nameParts, item.label];
                next.push({ attributes: attrs, image, nameParts });
            });
        });
        combinations = next;
    });

    let added = 0;
    combinations.forEach((comb) => {
        const canonical = canonicalizeAttributes(comb.attributes);
        if (existingSet.has(canonical)) return;

        const variantName = comb.nameParts.join(' / ');
        addVariantRow({
            name: variantName,
            sku: null,
            price: defaults.price,
            compare_at_price: defaults.compareAt,
            stock_quantity: defaults.stock,
            in_stock: defaults.inStock,
            image: comb.image,
            attributes: comb.attributes,
            is_active: true,
            sort_order: 0,
        });

        existingSet.add(canonical);
        added++;
    });

    alert(added ? `Generated ${added} variant(s).` : 'No new variants to add (already up to date).');
}

function addVariantRow(variant = {}) {
    const container = document.getElementById('variants-container');
    const index = variantIndex++;
    const attributesRaw = variant.attributes ?? null;
    let attributesObj = null;
    if (attributesRaw && typeof attributesRaw === 'object') {
        attributesObj = attributesRaw;
    } else if (typeof attributesRaw === 'string' && attributesRaw.trim() !== '') {
        try { attributesObj = JSON.parse(attributesRaw); } catch (e) { attributesObj = null; }
    }
    const attributesJson = attributesObj ? JSON.stringify(attributesObj) : (typeof attributesRaw === 'string' ? attributesRaw : '');
    const attributesText = attributesObj
        ? Object.entries(attributesObj).map(([k, v]) => `${k}=${v}`).join('\n')
        : '';
    const variantImage = variant.image ?? '';
    const row = document.createElement('div');
    row.className = 'variant-row p-3 border border-neutral-200 rounded-lg bg-white';
    row.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="hidden" name="variants[${index}][id]" value="${variant.id ?? ''}">
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">Variation Name</label>
                <input type="text" name="variants[${index}][name]" value="${variant.name ?? ''}" placeholder="e.g. Red / XL" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">SKU</label>
                <input type="text" name="variants[${index}][sku]" value="${variant.sku ?? ''}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">Price</label>
                <input type="number" step="0.01" min="0" name="variants[${index}][price]" value="${variant.price ?? ''}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">Stock</label>
                <input type="number" min="0" name="variants[${index}][stock_quantity]" value="${variant.stock_quantity ?? 0}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">Compare At Price</label>
                <input type="number" step="0.01" min="0" name="variants[${index}][compare_at_price]" value="${variant.compare_at_price ?? ''}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">Variant Image</label>
                <input type="hidden" name="variants[${index}][image]" value="${variantImage}" class="variant-image-input">
                <div class="flex items-center gap-2">
                    <div class="variant-image-preview w-10 h-10 rounded border border-neutral-200 overflow-hidden ${variantImage ? '' : 'hidden'}">
                        <img src="${variantImage}" alt="Variant image" class="w-full h-full object-cover">
                    </div>
                    <button type="button" onclick="uploadVariantImage(this)" class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition text-xs">
                        ${variantImage ? 'Change' : 'Upload'}
                    </button>
                    <button type="button" onclick="clearVariantImage(this)" class="px-3 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition text-xs ${variantImage ? '' : 'hidden'}">
                        Remove
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">Attributes (key=value)</label>
                <input type="hidden" name="variants[${index}][attributes]" id="variant-attributes-json-${index}" class="variant-attributes-json">
                <textarea id="variant-attributes-text-${index}" rows="2"
                          oninput="updateVariantAttributesJson(${index})"
                          placeholder="color=Red&#10;size=XL"
                          class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">${attributesText}</textarea>
                <p class="text-[11px] text-neutral-500 mt-1">One per line. Example: <span class="font-mono">color=Red</span></p>
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-600 mb-1">Sort Order</label>
                <input type="number" min="0" name="variants[${index}][sort_order]" value="${variant.sort_order ?? 0}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
            </div>
        </div>
        <div class="mt-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm text-neutral-700">
                    <input type="checkbox" name="variants[${index}][in_stock]" value="1" ${(variant.in_stock ?? true) ? 'checked' : ''} class="rounded border-neutral-300 text-primary focus:ring-primary">
                    In stock
                </label>
                <label class="flex items-center gap-2 text-sm text-neutral-700">
                    <input type="checkbox" name="variants[${index}][is_active]" value="1" ${(variant.is_active ?? true) ? 'checked' : ''} class="rounded border-neutral-300 text-primary focus:ring-primary">
                    Active
                </label>
            </div>
            <button type="button" onclick="removeVariantRow(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">Remove</button>
        </div>
    `;
    container.appendChild(row);

    const hiddenInput = row.querySelector(`#variant-attributes-json-${index}`);
    if (hiddenInput) hiddenInput.value = attributesJson || '';
}

function removeVariantRow(button) {
    button.closest('.variant-row').remove();
}

function uploadVariantImage(button) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'products');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = 'Uploading...';

        try {
            const response = await fetch('{{ route("admin.upload.image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });
            const data = await response.json();
            if (!data.success || !data.url) {
                alert('Failed to upload variant image');
                return;
            }

            const row = button.closest('.variant-row');
            const hiddenInput = row.querySelector('.variant-image-input');
            const previewWrap = row.querySelector('.variant-image-preview');
            const previewImg = previewWrap.querySelector('img');
            const removeBtn = button.nextElementSibling;

            hiddenInput.value = data.url;
            previewImg.src = data.url;
            previewWrap.classList.remove('hidden');
            button.textContent = 'Change';
            if (removeBtn) removeBtn.classList.remove('hidden');
        } catch (error) {
            console.error('Variant image upload failed:', error);
            alert('Error uploading variant image');
        } finally {
            button.disabled = false;
            if (button.textContent === 'Uploading...') {
                button.textContent = originalText;
            }
        }
    };
    input.click();
}

function clearVariantImage(button) {
    const row = button.closest('.variant-row');
    const hiddenInput = row.querySelector('.variant-image-input');
    const previewWrap = row.querySelector('.variant-image-preview');
    const previewImg = previewWrap.querySelector('img');
    const uploadBtn = previewWrap.parentElement.querySelector('button[onclick="uploadVariantImage(this)"]');

    hiddenInput.value = '';
    previewImg.src = '';
    previewWrap.classList.add('hidden');
    button.classList.add('hidden');
    if (uploadBtn) uploadBtn.textContent = 'Upload';
}

function keyValueLinesToJson(text) {
    const lines = (text || '')
        .split(/\r?\n/)
        .map(l => l.trim())
        .filter(l => l.length > 0 && !l.startsWith('#'));

    const obj = {};
    lines.forEach(line => {
        let sep = line.includes('=') ? '=' : (line.includes(':') ? ':' : null);
        if (!sep) return;
        const idx = line.indexOf(sep);
        const key = line.slice(0, idx).trim();
        const value = line.slice(idx + 1).trim();
        if (!key) return;
        obj[key] = value;
    });

    return obj;
}

function updateVariantAttributesJson(index) {
    const textarea = document.getElementById(`variant-attributes-text-${index}`);
    const hiddenInput = document.getElementById(`variant-attributes-json-${index}`);
    if (!textarea || !hiddenInput) return;

    const parsed = keyValueLinesToJson(textarea.value);
    hiddenInput.value = Object.keys(parsed).length ? JSON.stringify(parsed) : '';
}

function addImageUpload(imageUrl = '') {
    const container = document.getElementById('images-container');
    const index = imageIndex++;
    const imageId = `image-${index}`;
    
    const imageItem = document.createElement('div');
    imageItem.className = 'image-upload-item flex items-center gap-3 p-3 border border-neutral-300 rounded-lg';
    imageItem.dataset.index = index;
    
    imageItem.innerHTML = `
        <div class="image-preview flex-shrink-0 w-24 h-24 bg-neutral-100 rounded border border-neutral-200 overflow-hidden ${imageUrl ? '' : 'hidden'}">
            <img src="${imageUrl}" alt="Preview" class="w-full h-full object-cover">
        </div>
        <input type="hidden" name="images[]" value="${imageUrl}" class="image-url-input">
        <div class="flex-1">
            <button type="button" onclick="uploadImage(this)" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition text-sm">
                ${imageUrl ? 'Change Image' : 'Upload Image'}
            </button>
        </div>
        <button type="button" onclick="removeImage(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
            Remove
        </button>
    `;
    
    container.appendChild(imageItem);
}

function uploadImage(button) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = async function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'products'); // Store in products directory
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        button.disabled = true;
        button.textContent = 'Uploading...';
        
        try {
            const response = await fetch('{{ route("admin.upload.image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });
            
            const data = await response.json();
            if (data.success) {
                const item = button.closest('.image-upload-item');
                const preview = item.querySelector('.image-preview');
                const previewImg = preview.querySelector('img');
                const urlInput = item.querySelector('.image-url-input');
                
                urlInput.value = data.url;
                previewImg.src = data.url;
                preview.classList.remove('hidden');
                button.textContent = 'Change Image';
            } else {
                alert('Failed to upload image');
                button.textContent = 'Upload Image';
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Error uploading image');
            button.textContent = 'Upload Image';
        } finally {
            button.disabled = false;
        }
    };
    input.click();
}

function removeImage(button) {
    const item = button.closest('.image-upload-item');
    item.remove();
}

// Auto-generate product slug from name
document.addEventListener('DOMContentLoaded', function() {
    const productNameInput = document.getElementById('product-name');
    const productSlugInput = document.getElementById('product-slug');
    const originalName = productNameInput ? productNameInput.value : '';
    const originalSlug = productSlugInput ? productSlugInput.value : '';
    
    if (productNameInput && productSlugInput) {
        productNameInput.addEventListener('input', function() {
            if (!productSlugInput.dataset.manualEdit && productSlugInput.value === originalSlug) {
                productSlugInput.value = generateSlug(this.value);
            }
        });
        
        productSlugInput.addEventListener('input', function() {
            if (this.value !== originalSlug) {
                this.dataset.manualEdit = 'true';
            }
        });
    }
});

// Initialize with existing product images
document.addEventListener('DOMContentLoaded', function() {
    const productImages = @json(old('images', $product->images ?? []));
    if (productImages && productImages.length > 0) {
        productImages.forEach(url => {
            if (url) {
                addImageUpload(url);
            }
        });
    }
    
    // Initialize delivery options
    const deliveryOptionsJson = @json(old('order_delivery_options', $product->order_delivery_options ? json_decode($product->order_delivery_options, true) : []));
    if (deliveryOptionsJson && Array.isArray(deliveryOptionsJson) && deliveryOptionsJson.length > 0) {
        deliveryOptionsJson.forEach(option => {
            addDeliveryOption(option.name || '', option.charge || 0, option.days || '');
        });
    }

    const oldVariants = @json(old('variants'));
    const existingVariants = @json($existingVariantsForJs);
    const variantsToRender = Array.isArray(oldVariants) ? oldVariants : existingVariants;
    if (Array.isArray(variantsToRender) && variantsToRender.length > 0) {
        variantsToRender.forEach((variant) => addVariantRow(variant));
    }
});
</script>

<script>
let deliveryOptionIndex = 0;

function addDeliveryOption(name = '', charge = 0, days = '') {
    const container = document.getElementById('delivery-options-container');
    const index = deliveryOptionIndex++;
    
    const row = document.createElement('div');
    row.className = 'delivery-option-row flex flex-col sm:flex-row gap-3 p-4 border border-neutral-300 rounded-lg bg-neutral-50';
    row.dataset.index = index;
    
    row.innerHTML = `
        <div class="w-full sm:w-64 md:w-80">
            <label class="block text-xs font-medium text-neutral-600 mb-1">Name</label>
            <input type="text" class="delivery-name w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm" 
                   placeholder="e.g., Standard Delivery" value="${name}" onchange="updateDeliveryOptionsJson()">
        </div>
        <div class="w-full sm:w-32">
            <label class="block text-xs font-medium text-neutral-600 mb-1">Charge (৳)</label>
            <input type="number" class="delivery-charge w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm" 
                   placeholder="0" min="0" step="0.01" value="${charge}" onchange="updateDeliveryOptionsJson()">
        </div>
        <div class="w-full sm:w-32">
            <label class="block text-xs font-medium text-neutral-600 mb-1">Days</label>
            <input type="text" class="delivery-days w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm" 
                   placeholder="e.g., 3-5" value="${days}" onchange="updateDeliveryOptionsJson()">
        </div>
        <div class="flex items-end">
            <button type="button" onclick="removeDeliveryOption(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm whitespace-nowrap">
                Remove
            </button>
        </div>
    `;
    
    container.appendChild(row);
    updateDeliveryOptionsJson();
}

function removeDeliveryOption(button) {
    const row = button.closest('.delivery-option-row');
    row.remove();
    updateDeliveryOptionsJson();
}

function updateDeliveryOptionsJson() {
    const container = document.getElementById('delivery-options-container');
    const rows = container.querySelectorAll('.delivery-option-row');
    const options = [];
    
    rows.forEach(row => {
        const name = row.querySelector('.delivery-name').value.trim();
        const charge = parseFloat(row.querySelector('.delivery-charge').value) || 0;
        const days = row.querySelector('.delivery-days').value.trim();
        
        if (name) {
            options.push({
                name: name,
                charge: charge,
                days: days
            });
        }
    });
    
    const jsonInput = document.getElementById('order_delivery_options_json');
    jsonInput.value = options.length > 0 ? JSON.stringify(options) : '';
}
</script>

<!-- Quick Create Category Modal -->
<div id="quick-create-category-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">Quick Create Category</h2>
                <button type="button" onclick="closeQuickCreateCategoryModal()" class="text-neutral-400 hover:text-neutral-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="quick-create-category-form" class="space-y-4">
                @csrf
                <input type="hidden" name="is_active" value="1">
                <div>
                    <label for="quick-category-name" class="block text-sm font-medium text-neutral-700 mb-2">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" id="quick-category-name" name="name" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <div id="quick-category-name-error" class="mt-1 text-sm text-red-600 hidden"></div>
                </div>
                
                <div>
                    <label for="quick-category-slug" class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
                    <input type="text" id="quick-category-slug" name="slug" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <p class="mt-1 text-xs text-neutral-500">Auto-generated from name</p>
                </div>
                
                <div>
                    <label for="quick-category-image" class="block text-sm font-medium text-neutral-700 mb-2">Category Image</label>
                    <div class="flex items-center gap-3">
                        <div class="image-preview flex-shrink-0 w-24 h-24 bg-neutral-100 rounded border border-neutral-200 overflow-hidden hidden">
                            <img id="quick-category-image-preview" src="" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="quick-category-image" name="image" accept="image/*" class="hidden" onchange="handleQuickCategoryImageChange(this)">
                            <button type="button" onclick="document.getElementById('quick-category-image').click()" class="w-full px-4 py-2 border border-neutral-300 rounded-lg hover:bg-neutral-50 transition text-sm font-medium">
                                Choose Image
                            </button>
                            <p class="mt-1 text-xs text-neutral-500">Optional - JPG, PNG, GIF, WEBP (max 2MB)</p>
                        </div>
                        <button type="button" id="quick-category-image-remove" onclick="removeQuickCategoryImage()" class="hidden px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                            Remove
                        </button>
                    </div>
                    <input type="hidden" id="quick-category-image-url" name="image_url">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" id="quick-create-category-btn" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-medium">
                        Create
                    </button>
                    <button type="button" onclick="closeQuickCreateCategoryModal()" class="flex-1 bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openQuickCreateCategoryModal() {
    document.getElementById('quick-create-category-modal').classList.remove('hidden');
    document.getElementById('quick-category-name').focus();
}

function closeQuickCreateCategoryModal() {
    document.getElementById('quick-create-category-modal').classList.add('hidden');
    document.getElementById('quick-create-category-form').reset();
    document.getElementById('quick-category-name-error').classList.add('hidden');
    document.getElementById('quick-category-name-error').textContent = '';
    removeQuickCategoryImage();
}

function handleQuickCategoryImageChange(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image size must be less than 2MB');
        input.value = '';
        return;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('quick-category-image-preview');
        const previewDiv = preview.parentElement;
        const imageUrlInput = document.getElementById('quick-category-image-url');
        const removeBtn = document.getElementById('quick-category-image-remove');
        
        preview.src = e.target.result;
        previewDiv.classList.remove('hidden');
        imageUrlInput.value = ''; // Clear URL input, will use file
        removeBtn.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removeQuickCategoryImage() {
    const previewDiv = document.querySelector('#quick-category-image-preview').parentElement;
    const imageInput = document.getElementById('quick-category-image');
    const imageUrlInput = document.getElementById('quick-category-image-url');
    const removeBtn = document.getElementById('quick-category-image-remove');
    
    previewDiv.classList.add('hidden');
    imageInput.value = '';
    imageUrlInput.value = '';
    removeBtn.classList.add('hidden');
}

// Auto-generate slug
document.getElementById('quick-category-name').addEventListener('input', function() {
    const nameInput = this;
    const slugInput = document.getElementById('quick-category-slug');
    if (!slugInput.dataset.manualEdit) {
        slugInput.value = generateSlug(nameInput.value);
    }
});

document.getElementById('quick-category-slug').addEventListener('input', function() {
    this.dataset.manualEdit = 'true';
});

// Handle form submission
document.getElementById('quick-create-category-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('quick-create-category-btn');
    const nameError = document.getElementById('quick-category-name-error');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    nameError.classList.add('hidden');
    
    try {
        const response = await fetch('{{ route("admin.categories.store") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Add new category to dropdown
            const categorySelect = document.getElementById('category_id');
            const option = document.createElement('option');
            option.value = data.category.id;
            option.textContent = data.category.name;
            option.selected = true;
            categorySelect.appendChild(option);
            
            closeQuickCreateCategoryModal();
        } else {
            // Show errors
            if (data.errors && data.errors.name) {
                nameError.textContent = data.errors.name[0];
                nameError.classList.remove('hidden');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});

// Close modal on outside click
document.getElementById('quick-create-category-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickCreateCategoryModal();
    }
});

function generateSlug(text) {
    return text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim()
        .replace(/^-+|-+$/g, '');
}

function toggleFreeProduct(checkbox) {
    const priceInput = document.getElementById('product-price');
    if (checkbox.checked) {
        priceInput.value = 0;
        priceInput.disabled = true;
    } else {
        priceInput.disabled = false;
        if (priceInput.value == 0) {
            priceInput.value = '';
        }
    }
}

// Auto-check free checkbox when price is set to 0
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('product-price');
    const freeCheckbox = document.getElementById('product-is-free');
    
    if (priceInput && freeCheckbox) {
        priceInput.addEventListener('input', function() {
            if (parseFloat(this.value) === 0 && !this.disabled) {
                freeCheckbox.checked = true;
                this.disabled = true;
            } else if (parseFloat(this.value) > 0 && freeCheckbox.checked) {
                freeCheckbox.checked = false;
            }
        });
        
        // Check on page load if price is 0
        if (parseFloat(priceInput.value) === 0) {
            freeCheckbox.checked = true;
            priceInput.disabled = true;
        }
    }

    // Digital content section visibility
    const isDigitalSelect = document.getElementById('is_digital');
    const digitalSection = document.getElementById('digital-content-section');
    const fileWrap = document.getElementById('digital-file-wrap');
    const linkWrap = document.getElementById('digital-link-wrap');
    const fileRadio = document.querySelector('input[name="digital_content_type"][value="file"]');
    const linkRadio = document.querySelector('input[name="digital_content_type"][value="link"]');
    function toggleDigitalSection() {
        const isDigital = isDigitalSelect && isDigitalSelect.value === '1';
        if (digitalSection) digitalSection.classList.toggle('hidden', !isDigital);
    }
    function toggleFileLink() {
        const isFile = fileRadio && fileRadio.checked;
        if (fileWrap) fileWrap.classList.toggle('hidden', !isFile);
        if (linkWrap) linkWrap.classList.toggle('hidden', isFile);
    }
    if (isDigitalSelect) isDigitalSelect.addEventListener('change', toggleDigitalSection);
    if (fileRadio) fileRadio.addEventListener('change', toggleFileLink);
    if (linkRadio) linkRadio.addEventListener('change', toggleFileLink);
    toggleDigitalSection();
    toggleFileLink();
});
</script>
@endsection

