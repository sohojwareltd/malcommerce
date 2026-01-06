@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Create Product</h1>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6" onsubmit="updateDeliveryOptionsJson(); return true;">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Name *</label>
            <input type="text" name="name" id="product-name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
            <input type="text" name="slug" id="product-slug" value="{{ old('slug') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            <p class="mt-1 text-xs text-neutral-500">Auto-generated from name</p>
            @error('slug')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Category</label>
            <div class="flex gap-2">
                <select name="category_id" id="category_id" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="openQuickCreateCategoryModal()" class="px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg hover:bg-neutral-200 transition font-medium">
                    + Quick Add
                </button>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">SKU</label>
            <input type="text" name="sku" value="{{ old('sku') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Price *</label>
            <div class="flex items-center gap-3">
                <input type="number" name="price" id="product-price" step="0.01" value="{{ old('price') }}" required 
                       class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary" 
                       {{ old('is_free') ? 'disabled' : '' }}>
                <label class="flex items-center gap-2 whitespace-nowrap">
                    <input type="checkbox" name="is_free" id="product-is-free" value="1" 
                           {{ old('is_free') ? 'checked' : '' }} 
                           onchange="toggleFreeProduct(this)"
                           class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                    <span class="text-sm text-neutral-700">Free Product</span>
                </label>
            </div>
            @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Compare At Price</label>
            <input type="number" name="compare_at_price" step="0.01" value="{{ old('compare_at_price') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Stock Quantity *</label>
            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
    </div>

    <!-- Earning Settings -->
    <div class="mt-6 border-t border-neutral-200 pt-6">
        <h2 class="text-xl font-bold mb-4">Earning Settings</h2>
        <p class="text-sm text-neutral-600 mb-4">Configure customer cashback and sponsor commission for this product.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Cashback Amount (৳)</label>
                <input type="number" name="cashback_amount" step="0.01" min="0" value="{{ old('cashback_amount', 0) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">Amount given back to the customer per order.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Commission Type</label>
                <select name="commission_type" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="fixed" {{ old('commission_type', 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed (৳)</option>
                    <option value="percent" {{ old('commission_type') === 'percent' ? 'selected' : '' }}>Percent (%)</option>
                </select>
                <p class="text-xs text-neutral-500 mt-1">How sponsor commission is calculated.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Commission Value</label>
                <input type="number" name="commission_value" step="0.01" min="0" value="{{ old('commission_value', 0) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
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
        <textarea name="short_description" rows="2" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('short_description') }}</textarea>
    </div>
    
    <div class="mt-6">
        <label class="block text-sm font-medium text-neutral-700 mb-2">Description</label>
        <textarea name="description" rows="5" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
    </div>
    
    <div class="mt-6 flex gap-4">
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-neutral-300 text-primary focus:ring-primary">
            <span class="ml-2 text-sm text-neutral-700">Active</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-neutral-300 text-primary focus:ring-primary">
            <span class="ml-2 text-sm text-neutral-700">Featured</span>
        </label>
    </div>
    
    <!-- Order Form Settings -->
    <div class="mt-8 border-t border-neutral-200 pt-6">
        <h2 class="text-xl font-bold mb-4">Order Form Settings</h2>
        <p class="text-sm text-neutral-600 mb-4">Customize the order form for this product. Leave empty to use global settings.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Order Form Title</label>
                <input type="text" name="order_form_title" value="{{ old('order_form_title') }}" placeholder="Leave empty for default" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">Default: "অর্ডার করুন"</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Order Button Text</label>
                <input type="text" name="order_button_text" value="{{ old('order_button_text') }}" placeholder="Leave empty for default" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">Default: "অর্ডার নিশ্চিত করুন"</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Minimum Order Quantity</label>
                <input type="number" name="order_min_quantity" value="{{ old('order_min_quantity') }}" min="0" step="1" placeholder="0 = no minimum" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">Minimum number of items required per order</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Maximum Order Quantity</label>
                <input type="number" name="order_max_quantity" value="{{ old('order_max_quantity') }}" min="0" step="1" placeholder="0 = no maximum" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
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
                <input type="hidden" name="order_delivery_options" id="order_delivery_options_json" value="{{ old('order_delivery_options', '') }}">
            </div>
        </div>
        <div class="mt-4 space-y-2">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="order_hide_summary" value="1" {{ old('order_hide_summary') ? 'checked' : '' }} class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                <span class="text-sm font-medium text-neutral-700">Hide Order Summary</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="order_hide_quantity" value="1" {{ old('order_hide_quantity') ? 'checked' : '' }} class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary">
                <span class="text-sm font-medium text-neutral-700">Hide Quantity Selector</span>
            </label>
        </div>
    </div>
    
    <div class="mt-6">
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
            Create Product
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-4 text-neutral-700 hover:text-neutral-900">Cancel</a>
    </div>
</form>

<script>
let imageIndex = 0;

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
                const index = parseInt(item.dataset.index);
                
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
    
    if (productNameInput && productSlugInput) {
        productNameInput.addEventListener('input', function() {
            if (!productSlugInput.dataset.manualEdit) {
                productSlugInput.value = generateSlug(this.value);
            }
        });
        
        productSlugInput.addEventListener('input', function() {
            this.dataset.manualEdit = 'true';
        });
    }
});

// Initialize with any existing images from old input
document.addEventListener('DOMContentLoaded', function() {
    const oldImages = @json(old('images', []));
    if (oldImages && oldImages.length > 0) {
        oldImages.forEach(url => {
            if (url) {
                addImageUpload(url);
            }
        });
    }
    
    // Initialize delivery options from old input
    const oldDeliveryOptions = @json(old('order_delivery_options', ''));
    if (oldDeliveryOptions) {
        try {
            const parsed = typeof oldDeliveryOptions === 'string' ? JSON.parse(oldDeliveryOptions) : oldDeliveryOptions;
            if (Array.isArray(parsed) && parsed.length > 0) {
                parsed.forEach(option => {
                    addDeliveryOption(option.name || '', option.charge || 0, option.days || '');
                });
            }
        } catch (e) {
            console.error('Error parsing delivery options:', e);
        }
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
    }
});
</script>
@endsection


