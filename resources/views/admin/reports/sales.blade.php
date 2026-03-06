@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold">Sales Report</h1>
        <p class="text-neutral-600 mt-2">View detailed sales statistics and analytics</p>
    </div>
    <a href="{{ route('admin.reports.sales.export', request()->query()) }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm flex items-center gap-2 self-start">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Export CSV
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('admin.reports.sales') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div>
            <label for="date_from" class="block text-sm font-medium text-neutral-700 mb-2">From Date</label>
            <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-2">To Date</label>
            <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label for="product_id" class="block text-sm font-medium text-neutral-700 mb-2">Product</label>
            <select name="product_id" id="product_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Products</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="product_type" class="block text-sm font-medium text-neutral-700 mb-2">Product Type</label>
            <select name="product_type" id="product_type" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Types</option>
                <option value="physical" {{ request('product_type') === 'physical' ? 'selected' : '' }}>Physical</option>
                <option value="digital" {{ request('product_type') === 'digital' ? 'selected' : '' }}>Digital</option>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All (excl. cancelled)</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6 mb-6">
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
                <p class="text-sm font-medium text-neutral-500">Revenue (Delivered)</p>
                <p class="text-3xl font-bold mt-2 text-green-600">৳{{ number_format($stats['revenue'], 2) }}</p>
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
                <p class="text-sm font-medium text-neutral-500">Pending Revenue</p>
                <p class="text-3xl font-bold mt-2 text-amber-600">৳{{ number_format($stats['pending_revenue'], 2) }}</p>
            </div>
            <div class="bg-amber-100 rounded-full p-3">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <p class="text-xs text-neutral-500 mt-2">Pending, processing, shipped</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-neutral-500">Avg Order (Delivered)</p>
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

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-neutral-500">Expenses</p>
                <p class="text-3xl font-bold mt-2 text-red-600">৳{{ number_format($stats['total_expenses'], 2) }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
        <p class="text-xs text-neutral-500 mt-2">In date range</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 {{ $stats['profit'] >= 0 ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-neutral-500">Profit</p>
                <p class="text-3xl font-bold mt-2 {{ $stats['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">৳{{ number_format($stats['profit'], 2) }}</p>
            </div>
            <div class="{{ $stats['profit'] >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-full p-3">
                <svg class="w-8 h-8 {{ $stats['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
        <p class="text-xs text-neutral-500 mt-2">Revenue − Expenses</p>
    </div>
</div>

<!-- Orders by Product Type -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4">By Product Type</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($stats['by_product_type'] as $type => $data)
        <div class="border rounded-lg p-4 {{ $type === 'physical' ? 'border-amber-200 bg-amber-50/50' : 'border-indigo-200 bg-indigo-50/50' }}">
            <p class="text-sm font-medium text-neutral-600">{{ $data['name'] }}</p>
            <p class="text-2xl font-bold mt-1">{{ $data['count'] }} orders</p>
            <p class="text-sm text-neutral-600 mt-2">
                <span class="text-green-700 font-medium">Revenue: ৳{{ number_format($data['revenue'], 2) }}</span>
                <span class="text-neutral-400 mx-1">·</span>
                <span class="text-amber-700">Pending: ৳{{ number_format($data['pending_revenue'], 2) }}</span>
            </p>
        </div>
        @endforeach
    </div>
</div>

<!-- By Product -->
@if($stats['by_product']->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4">By Product</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Orders</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Revenue</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Pending</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @foreach($stats['by_product'] as $row)
                <tr class="hover:bg-neutral-50">
                    <td class="px-4 py-3 text-sm font-medium text-neutral-900">{{ $row['name'] }}</td>
                    <td class="px-4 py-3 text-sm text-neutral-600 text-right">{{ $row['count'] }}</td>
                    <td class="px-4 py-3 text-sm text-neutral-600 text-right">{{ $row['quantity'] }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-700 text-right">৳{{ number_format($row['revenue'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-amber-700 text-right">৳{{ number_format($row['pending_revenue'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Orders by Status -->
@if($stats['by_status']->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4">Orders by Status</h2>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach($stats['by_status'] as $status => $data)
        <div class="border-l-4 {{ $status === 'pending' ? 'border-yellow-500' : ($status === 'processing' ? 'border-blue-500' : ($status === 'shipped' ? 'border-purple-500' : ($status === 'delivered' ? 'border-green-500' : 'border-red-500'))) }} pl-4">
            <p class="text-sm font-medium text-neutral-500">{{ ucfirst($status) }}</p>
            <p class="text-2xl font-bold mt-1">{{ $data['count'] }}</p>
            @if($status === 'delivered')
                <p class="text-sm text-green-600 mt-1 font-medium">৳{{ number_format($data['revenue'], 2) }}</p>
            @elseif($status !== 'cancelled')
                <p class="text-sm text-amber-600 mt-1">Pending: ৳{{ number_format($data['pending_revenue'], 2) }}</p>
            @endif
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

    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($orders->take(20) as $order)
                @php
                    $rowBg = match($order->status) {
                        'pending' => 'bg-yellow-50',
                        'processing' => 'bg-blue-50',
                        'shipped' => 'bg-purple-50',
                        'delivered' => 'bg-green-50',
                        'cancelled' => 'bg-red-50',
                        default => 'bg-white',
                    };
                @endphp
                <tr class="{{ $rowBg }} hover:brightness-95">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->product?->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-0.5 text-xs rounded {{ $order->product && $order->product->is_digital ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $order->product && $order->product->is_digital ? 'Digital' : 'Physical' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">৳{{ number_format($order->total_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                               ($order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'))) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline font-semibold">View</a>
                        <a href="{{ route('admin.orders.edit', $order) }}" class="text-neutral-600 hover:text-neutral-800 font-semibold">Edit</a>
                        <button type="button" class="text-red-600 hover:text-red-800 font-semibold" data-delete-url="{{ route('admin.orders.destroy', $order) }}" onclick="deleteSingleOrder(this)">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-neutral-500">No orders found for the selected period</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($orders->take(20) as $order)
        @php
            $cardBg = match($order->status) {
                'pending' => 'bg-yellow-50',
                'processing' => 'bg-blue-50',
                'shipped' => 'bg-purple-50',
                'delivered' => 'bg-green-50',
                'cancelled' => 'bg-red-50',
                default => 'bg-white',
            };
            $statusBadge = match($order->status) {
                'pending' => 'bg-yellow-100 text-yellow-800',
                'processing' => 'bg-blue-100 text-blue-800',
                'shipped' => 'bg-purple-100 text-purple-800',
                'delivered' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
                default => 'bg-neutral-100 text-neutral-800',
            };
        @endphp
        <div class="p-4 {{ $cardBg }}">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline font-semibold text-sm">#{{ $order->order_number }}</a>
                    <p class="text-xs text-neutral-600">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex flex-col items-end gap-1 ml-2">
                    <span class="text-sm font-bold text-neutral-900">৳{{ number_format($order->total_price, 2) }}</span>
                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
            <div class="space-y-1 text-sm">
                <div><span class="text-neutral-500">Product:</span> <span class="font-medium">{{ $order->product?->name ?? '—' }}</span></div>
                <div><span class="text-neutral-500">Type:</span> <span class="px-2 py-0.5 text-xs rounded {{ $order->product && $order->product->is_digital ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">{{ $order->product && $order->product->is_digital ? 'Digital' : 'Physical' }}</span></div>
                <div><span class="text-neutral-500">Customer:</span> <span class="font-medium">{{ $order->customer_name }}</span></div>
            </div>
            <div class="mt-3 pt-3 border-t border-neutral-200 flex items-center justify-between gap-2">
                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline font-semibold text-sm">View</a>
                <a href="{{ route('admin.orders.edit', $order) }}" class="text-neutral-600 hover:text-neutral-800 font-semibold text-sm">Edit</a>
                <button type="button" class="text-red-600 hover:text-red-800 font-semibold text-sm" data-delete-url="{{ route('admin.orders.destroy', $order) }}" onclick="deleteSingleOrder(this)">Delete</button>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">No orders found for the selected period</div>
        @endforelse
    </div>
</div>

<form id="single-delete-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function deleteSingleOrder(button) {
        if (!confirm('Are you sure you want to delete this order?')) return;
        const url = button.getAttribute('data-delete-url');
        const form = document.getElementById('single-delete-form');
        if (url && form) { form.action = url; form.submit(); }
    }
</script>
@endpush










