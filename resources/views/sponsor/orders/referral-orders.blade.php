@extends('layouts.sponsor')

@section('title', 'Referral Orders')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Referral Orders</h1>
        <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Orders from your referrals (these count as revenue)</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('sponsor.orders.my-orders') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold text-sm">
            View My Orders
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-6 mb-4 sm:mb-6">
    <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 border border-neutral-200">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-purple-500 rounded-lg p-1.5 sm:p-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-[10px] sm:text-xs text-neutral-500 mb-0.5 sm:mb-1 truncate">Total Orders</h3>
        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-neutral-900">{{ $stats['total_orders'] }}</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 border border-neutral-200">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-green-500 rounded-lg p-1.5 sm:p-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-[10px] sm:text-xs text-neutral-500 mb-0.5 sm:mb-1 truncate">Total Revenue</h3>
        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-green-600">৳{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
        <p class="text-[8px] sm:text-[10px] text-green-600 mt-1">(Counted as revenue)</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 border border-neutral-200">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-yellow-500 rounded-lg p-1.5 sm:p-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-[10px] sm:text-xs text-neutral-500 mb-0.5 sm:mb-1 truncate">Pending</h3>
        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 border border-neutral-200">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-primary rounded-lg p-1.5 sm:p-2 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-[10px] sm:text-xs text-neutral-500 mb-0.5 sm:mb-1 truncate">Delivered</h3>
        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-primary">{{ $stats['delivered_orders'] }}</p>
    </div>
</div>

<!-- Search and Filter Form -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('sponsor.orders.referral-orders') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by order number, customer name, phone, or product..."
                           class="w-full px-4 py-2 pl-10 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
                <select name="status" 
                        id="status"
                        class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <!-- Per Page -->
            <div>
                <label for="per_page" class="block text-sm font-medium text-neutral-700 mb-2">Per Page</label>
                <select name="per_page" 
                        id="per_page"
                        class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page') == '20' || !request('per_page') ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-neutral-700 mb-2">Date From</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from" 
                       value="{{ request('date_from') }}" 
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base">
            </div>
            
            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-2">Date To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to" 
                       value="{{ request('date_to') }}" 
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base">
            </div>
            
            <!-- Action Buttons -->
            <div class="lg:col-span-2 flex flex-col sm:flex-row gap-2 items-end">
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold shadow-md hover:shadow-lg text-sm sm:text-base w-full sm:w-auto">
                    Apply Filters
                </button>
                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to', 'per_page']))
                <a href="{{ route('sponsor.orders.referral-orders') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base text-center w-full sm:w-auto">
                    Clear Filters
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-purple-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-purple-900 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-purple-900 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-purple-900 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-purple-900 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-purple-900 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-purple-900 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-purple-900 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($orders as $order)
                <tr class="hover:bg-purple-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-neutral-900">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-700">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-700">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->customer_phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                               ($order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : 
                               ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               'bg-red-100 text-red-800'))) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-600">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-neutral-500">
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            No orders found matching your filters
                        @else
                            No referral orders yet
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($orders as $order)
        <div class="p-4 hover:bg-purple-50 transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="text-xs font-mono text-neutral-500">#</span>
                        <span class="text-sm font-semibold text-neutral-900 truncate">{{ $order->order_number }}</span>
                    </div>
                    <p class="text-xs text-neutral-500">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 ml-2">
                    <span class="text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</span>
                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                           ($order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : 
                           ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                           'bg-red-100 text-red-800'))) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="space-y-1.5 text-sm">
                <div>
                    <span class="text-neutral-500 text-xs">Product:</span>
                    <span class="text-neutral-900 font-medium ml-1">{{ $order->product->name }}</span>
                </div>
                <div>
                    <span class="text-neutral-500 text-xs">Customer:</span>
                    <span class="text-neutral-900 font-medium ml-1">{{ $order->customer_name }}</span>
                </div>
                <div>
                    <span class="text-neutral-500 text-xs">Phone:</span>
                    <span class="text-neutral-900 font-medium ml-1">{{ $order->customer_phone }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">
            @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p>No orders found matching your filters</p>
                </div>
            @else
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p>No referral orders yet</p>
                </div>
            @endif
        </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection


