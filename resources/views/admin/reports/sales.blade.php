@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Sales Report</h1>
    <p class="text-neutral-600 mt-2">View detailed sales statistics and analytics</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('admin.reports.sales') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="date_from" class="block text-sm font-medium text-neutral-700 mb-2">From Date</label>
            <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-2">To Date</label>
            <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-neutral-500">Total Orders</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-neutral-500">Total Revenue</p>
                <p class="text-3xl font-bold mt-2 text-green-600">৳{{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-neutral-500">Average Order Value</p>
                <p class="text-3xl font-bold mt-2">৳{{ number_format($stats['average_order_value'], 2) }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-neutral-500">Items Sold</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['total_items_sold'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Orders by Status -->
@if($stats['by_status']->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4">Orders by Status</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach($stats['by_status'] as $status => $data)
        <div class="border-l-4 {{ $status === 'pending' ? 'border-yellow-500' : ($status === 'processing' ? 'border-blue-500' : ($status === 'shipped' ? 'border-purple-500' : ($status === 'delivered' ? 'border-green-500' : 'border-red-500'))) }} pl-4">
            <p class="text-sm font-medium text-neutral-500">{{ ucfirst($status) }}</p>
            <p class="text-2xl font-bold mt-1">{{ $data['count'] }}</p>
            <p class="text-sm text-neutral-600 mt-1">৳{{ number_format($data['revenue'], 2) }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-neutral-200">
        <h2 class="text-xl font-bold">Recent Orders</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($orders->take(20) as $order)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">৳{{ number_format($order->total_price, 2) }}</td>
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
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-neutral-500">No orders found for the selected period</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


