@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-6 mb-6 sm:mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-2 sm:p-3 lg:p-6 text-white transform hover:scale-105 transition-transform duration-200 min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-white/20 rounded-lg p-1 sm:p-1.5 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-white/80 text-[10px] sm:text-xs font-medium mb-0.5 sm:mb-1 truncate">Total Orders</h3>
        <p class="text-lg sm:text-xl lg:text-3xl xl:text-4xl font-bold truncate">{{ $stats['total_orders'] }}</p>
    </div>
    
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-2 sm:p-3 lg:p-6 text-white transform hover:scale-105 transition-transform duration-200 min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-white/20 rounded-lg p-1 sm:p-1.5 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-white/80 text-[10px] sm:text-xs font-medium mb-0.5 sm:mb-1 truncate">Pending Orders</h3>
        <p class="text-lg sm:text-xl lg:text-3xl xl:text-4xl font-bold truncate">{{ $stats['pending_orders'] }}</p>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-2 sm:p-3 lg:p-6 text-white transform hover:scale-105 transition-transform duration-200 min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-white/20 rounded-lg p-1 sm:p-1.5 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-white/80 text-[10px] sm:text-xs font-medium mb-0.5 sm:mb-1 truncate">Total Products</h3>
        <p class="text-lg sm:text-xl lg:text-3xl xl:text-4xl font-bold truncate">{{ $stats['total_products'] }}</p>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-2 sm:p-3 lg:p-6 text-white transform hover:scale-105 transition-transform duration-200 min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-white/20 rounded-lg p-1 sm:p-1.5 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-white/80 text-[10px] sm:text-xs font-medium mb-0.5 sm:mb-1 truncate">Total Revenue</h3>
        <p class="text-lg sm:text-xl lg:text-3xl xl:text-4xl font-bold truncate">৳{{ number_format($stats['total_revenue'], 2) }}</p>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3 lg:gap-6 mb-6 sm:mb-8">
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl shadow-lg p-2 sm:p-3 lg:p-6 text-white min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-white/20 rounded-lg p-1 sm:p-1.5 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-white/80 text-[10px] sm:text-xs font-medium mb-0.5 sm:mb-1 truncate">Sponsors</h3>
        <p class="text-lg sm:text-xl lg:text-3xl xl:text-4xl font-bold truncate">{{ $stats['total_sponsors'] }}</p>
    </div>
    
    <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl shadow-lg p-2 sm:p-3 lg:p-6 text-white min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-white/20 rounded-lg p-1 sm:p-1.5 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-white/80 text-[10px] sm:text-xs font-medium mb-0.5 sm:mb-1 truncate">Growth Rate</h3>
        <p class="text-lg sm:text-xl lg:text-3xl xl:text-4xl font-bold truncate">+12%</p>
    </div>
    
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-2 sm:p-3 lg:p-6 text-white min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-white/20 rounded-lg p-1 sm:p-1.5 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-white/80 text-[10px] sm:text-xs font-medium mb-0.5 sm:mb-1 truncate">Avg Order Value</h3>
        <p class="text-lg sm:text-xl lg:text-3xl xl:text-4xl font-bold truncate">৳{{ number_format($stats['total_revenue'] / max($stats['total_orders'], 1), 2) }}</p>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 border border-neutral-100 overflow-x-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-2">
        <h2 class="text-xl sm:text-2xl font-bold text-neutral-800">Recent Orders</h2>
        <a href="{{ route('admin.orders.index') }}" class="text-primary hover:text-primary-light font-semibold text-sm">View All →</a>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-gradient-to-r from-neutral-50 to-neutral-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-100">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-neutral-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-neutral-900">{{ $order->order_number }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-green-600">৳{{ number_format($order->total_price, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                               'bg-blue-100 text-blue-800')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-neutral-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>No orders yet</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($recentOrders as $order)
        <a href="{{ route('admin.orders.show', $order) }}" class="block bg-white rounded-lg border border-neutral-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-neutral-50 to-neutral-100 px-3 py-2.5 border-b border-neutral-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <span class="text-xs font-mono text-neutral-500">#</span>
                        <span class="text-sm font-bold text-neutral-900 truncate">{{ $order->order_number }}</span>
                    </div>
                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full whitespace-nowrap ml-2
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                           'bg-blue-100 text-blue-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            
            <!-- Content Section -->
            <div class="p-3">
                <!-- Amount and Date Row -->
                <div class="flex items-center justify-between mb-3 pb-3 border-b border-neutral-100">
                    <div>
                        <p class="text-xs text-neutral-500 mb-0.5">Amount</p>
                        <p class="text-lg font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-500 mb-0.5">Date</p>
                        <p class="text-sm font-medium text-neutral-700">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                <!-- Product and Customer Info -->
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-neutral-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-neutral-500 mb-0.5">Product</p>
                            <p class="text-sm font-medium text-neutral-900 truncate">{{ $order->product->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-neutral-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-neutral-500 mb-0.5">Customer</p>
                            <p class="text-sm font-medium text-neutral-900 truncate">{{ $order->customer_name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-8 text-neutral-500">
            <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-neutral-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p>No orders yet</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

