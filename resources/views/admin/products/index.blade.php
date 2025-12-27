@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
        Add New Product
    </a>
</div>

<!-- Search and Filter Form -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, slug, SKU, or description..."
                           class="w-full px-4 py-2 pl-10 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-neutral-700 mb-2">Category</label>
                <select name="category" id="category" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
                Search
            </button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.products.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold">
                    Clear Filters
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-neutral-200">
        <thead class="bg-neutral-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-neutral-200">
            @forelse($products as $product)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-neutral-900">{{ $product->name }}</div>
                    <div class="text-sm text-neutral-500">{{ $product->slug }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                    {{ $product->category->name ?? 'Uncategorized' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                    ৳{{ number_format($product->price, 2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                    {{ $product->stock_quantity }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-primary hover:text-primary-light">Edit</a>
                        <span class="text-neutral-300">|</span>
                        <a href="{{ route('admin.products.builder', $product) }}" class="text-blue-600 hover:text-blue-800">Page Builder</a>
                        <span class="text-neutral-300">|</span>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-neutral-500">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection


