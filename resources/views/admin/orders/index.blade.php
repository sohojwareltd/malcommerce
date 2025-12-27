@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Orders</h1>
</div>

<!-- Search and Filter Form -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by order number, customer name, phone, product, or sponsor..."
                           class="w-full px-4 py-2 pl-10 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <!-- Date Range (Optional) -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-neutral-700 mb-2">Date From</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from" 
                       value="{{ request('date_from') }}" 
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-2">Date To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to" 
                       value="{{ request('date_to') }}" 
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
                Search
            </button>
            @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold">
                    Clear Filters
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Sponsor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($orders as $order)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        <div>{{ $order->customer_name }}</div>
                        <div class="text-xs text-neutral-400">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">৳{{ number_format($order->total_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $order->sponsor->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                               'bg-blue-100 text-blue-800')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:text-primary-light font-medium">View Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-neutral-500">No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection


