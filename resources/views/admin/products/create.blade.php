@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Create Product</h1>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            @error('slug')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Category</label>
            <select name="category_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">SKU</label>
            <input type="text" name="sku" value="{{ old('sku') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Price *</label>
            <input type="number" name="price" step="0.01" value="{{ old('price') }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
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
    
    <div class="mt-6">
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
            Create Product
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-4 text-neutral-700 hover:text-neutral-900">Cancel</a>
    </div>
</form>
@endsection


