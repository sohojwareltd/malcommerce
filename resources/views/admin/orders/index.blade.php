@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="mb-4 sm:mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold">Orders</h1>
</div>

<!-- Search and Filter Form -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by order number, customer name, phone, product, or sponsor..."
                           class="w-full px-4 py-2 pl-10 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
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
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
            </div>
            
            <!-- Per Page -->
            <div>
                <label for="per_page" class="block text-sm font-medium text-neutral-700 mb-2">Per Page</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                    <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-2">Date To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to" 
                       value="{{ request('date_to') }}" 
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <button type="submit" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base">
                Search
            </button>
            @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}{{ request('per_page') ? '?per_page=' . request('per_page') : '' }}" class="bg-neutral-200 text-neutral-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base text-center">
                    Clear Filters
                </a>
            @endif
        </div>
        <!-- Preserve per_page when clearing filters -->
        @if(request('per_page'))
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
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
                @php
                    $rowBgClass = match($order->status) {
                        'pending' => 'bg-yellow-100 hover:bg-yellow-200',
                        'processing' => 'bg-blue-100 hover:bg-blue-200',
                        'shipped' => 'bg-indigo-100 hover:bg-indigo-200',
                        'delivered' => 'bg-green-100 hover:bg-green-200',
                        'cancelled' => 'bg-red-100 hover:bg-red-200',
                        default => 'bg-neutral-100 hover:bg-neutral-200'
                    };
                @endphp
                <tr class="{{ $rowBgClass }} transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline font-semibold">{{ $order->order_number }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-800">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-800">
                        <div>{{ $order->customer_name }}</div>
                        <div class="text-xs text-neutral-600">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-neutral-900">৳{{ number_format($order->total_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-800">
                        {{ $order->sponsor->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                               ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                               ($order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800' :
                               'bg-gray-100 text-gray-800')))) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-800">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:text-primary-light font-semibold">View Details</a>
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
    
    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($orders as $order)
        @php
            $cardBgClass = match($order->status) {
                'pending' => 'bg-yellow-50',
                'processing' => 'bg-blue-50',
                'shipped' => 'bg-indigo-50',
                'delivered' => 'bg-green-50',
                'cancelled' => 'bg-red-50',
                default => 'bg-neutral-50'
            };
        @endphp
        <div class="p-4 {{ $cardBgClass }} transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline font-semibold text-sm mb-1 block">#{{ $order->order_number }}</a>
                    <p class="text-xs text-neutral-600">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 ml-2">
                    <span class="text-sm font-bold text-neutral-900">৳{{ number_format($order->total_price, 2) }}</span>
                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                           ($order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800' :
                           'bg-gray-100 text-gray-800')))) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-neutral-500">Product:</span>
                    <span class="text-neutral-900 font-medium ml-1">{{ $order->product->name }}</span>
                </div>
                <div>
                    <span class="text-neutral-500">Customer:</span>
                    <span class="text-neutral-900 font-medium ml-1">{{ $order->customer_name }}</span>
                    <span class="text-neutral-600 text-xs ml-2">({{ $order->customer_phone }})</span>
                </div>
                @if($order->sponsor)
                <div>
                    <span class="text-neutral-500">Sponsor:</span>
                    <span class="text-neutral-900 font-medium ml-1">{{ $order->sponsor->name }}</span>
                </div>
                @endif
            </div>
            <div class="mt-3 pt-3 border-t border-neutral-200">
                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:text-primary-light font-semibold text-sm">View Details →</a>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">
            No orders found
        </div>
        @endforelse
    </div>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection


