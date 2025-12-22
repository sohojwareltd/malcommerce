@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
        Add New Product
    </a>
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
                    <a href="{{ route('admin.products.edit', $product) }}" class="text-primary hover:text-primary-light mr-3">Edit</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                    </form>
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


