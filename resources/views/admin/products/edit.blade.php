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
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Category</label>
            <select name="category_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
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
    
    <!-- Theme Builder Link -->
    <div class="mt-8 border-t pt-6">
        <h2 class="text-xl font-bold mb-4">Page Layout Builder</h2>
        <p class="text-neutral-600 mb-4">Create custom sections for this product page using our drag-and-drop builder.</p>
        <a href="{{ route('admin.products.builder', $product) }}" class="inline-block bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-light transition font-semibold">
            Open Page Builder →
        </a>
        @if($product->page_layout && count($product->page_layout) > 0)
        <p class="text-sm text-accent mt-2">✓ {{ count($product->page_layout) }} section(s) configured</p>
        @endif
    </div>
    
    <div class="mt-6">
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
            Update Product
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-4 text-neutral-700 hover:text-neutral-900">Cancel</a>
    </div>
</form>
@endsection

