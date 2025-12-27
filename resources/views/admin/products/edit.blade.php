@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Edit Product</h1>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
    @csrf
    @method('PUT')
    
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
            <label class="block text-sm font-medium text-neutral-700 mb-2">Price *</label>
            <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
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
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-neutral-300 text-primary focus:ring-primary">
            <span class="ml-2 text-sm text-neutral-700">Featured</span>
        </label>
    </div>
    
    <div class="mt-6">
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
            Update Product
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
});
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
</script>
@endsection

