                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           @extends('layouts.sponsor')

@section('title', 'Dashboard')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@section('content')
<style>
    :root {
        --color-dark: #0F2854;
        --color-medium: #1C4D8D;
        --color-light: #4988C4;
        --color-accent: #BDE8F5;
    }
    
    .app-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 40, 84, 0.08);
        transition: all 0.3s ease;
    }
    
    .app-card:hover {
        box-shadow: 0 4px 16px rgba(15, 40, 84, 0.12);
    }
    
    .stat-card {
        background: linear-gradient(135deg, var(--color-medium) 0%, var(--color-light) 100%);
        border-radius: 16px;
        padding: 20px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 150%;
        height: 150%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    
    .income-card {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    }
    
    .nav-tab {
        padding: 8px 12px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
        font-size: 0.75rem;
    }
    
    @media (min-width: 640px) {
        .nav-tab {
            padding: 12px 20px;
            font-size: 0.875rem;
        }
    }
    
    .nav-tab.active {
        background: var(--color-medium);
        color: white;
    }
    
    .nav-tab:not(.active) {
        background: var(--color-accent);
        color: var(--color-dark);
    }
</style>

<div class="min-h-screen pb-6" style="">
    <!-- Header Profile Section -->
    <div class="app-card mx-4 mt-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-medium) 100%);">
        <div class="p-4">
            <div class="flex items-center gap-4 mb-4">
                <div class="relative">
                        @if(Auth::user()->photo)
                        <img src="{{ Storage::disk('public')->url(Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" 
                             class="w-16 h-16 rounded-full object-cover border-[3px] border-white shadow-lg">
                        @else
                        <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] border-white shadow-lg" style="background: var(--color-light);">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    <div class="absolute bottom-0 right-0 bg-green-500 border-[3px] border-white rounded-full w-4 h-4"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                    <h1 class="text-base sm:text-lg md:text-xl font-bold text-white truncate mb-1">{{ Auth::user()->name }}</h1>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] sm:text-xs font-mono text-white/90 bg-white/20 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg">{{ Auth::user()->affiliate_code }}</span>
                        @if(Auth::user()->phone)
                        <span class="text-[10px] sm:text-xs text-white/80">{{ Auth::user()->phone }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('sponsor.profile.edit') }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
            </div>

            <!-- Income Display -->
            <div class="income-card rounded-xl p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/90 text-xs sm:text-sm font-medium mb-1">Available Balance</p>
                        <p class="text-xl sm:text-2xl md:text-3xl font-bold text-white">৳{{ number_format(Auth::user()->balance ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-2 sm:p-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-3 gap-2 sm:gap-3 px-4 mb-4">
        <div class="stat-card text-center" style="padding: 12px 8px;">
            <div class="mb-1 sm:mb-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mx-auto text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <p class="text-[10px] sm:text-xs text-white/80 mb-0.5 sm:mb-1">Referrals</p>
            <p class="text-base sm:text-lg md:text-xl font-bold text-white">{{ $stats['total_referrals'] }}</p>
        </div>
        
        <div class="stat-card text-center" style="padding: 12px 8px;">
            <div class="mb-1 sm:mb-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mx-auto text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
        </div>
            <p class="text-[10px] sm:text-xs text-white/80 mb-0.5 sm:mb-1">Orders</p>
            <p class="text-base sm:text-lg md:text-xl font-bold text-white">{{ $stats['total_orders'] }}</p>
</div>

        <div class="stat-card text-center" style="padding: 12px 8px;">
            <div class="mb-1 sm:mb-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mx-auto text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-[10px] sm:text-xs text-white/80 mb-0.5 sm:mb-1">Pending</p>
            <p class="text-base sm:text-lg md:text-xl font-bold text-white">{{ $stats['pending_orders'] }}</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="px-4 mb-4">
        <div class="flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: none;">
            <button onclick="showSection('referrals')" class="nav-tab active whitespace-nowrap" id="tab-referrals">Referrals</button>
            <button onclick="showSection('orders')" class="nav-tab whitespace-nowrap" id="tab-orders">Recent Orders</button>
            <button onclick="showSection('links')" class="nav-tab whitespace-nowrap" id="tab-links">Referral Links</button>
            <button onclick="showSection('products')" class="nav-tab whitespace-nowrap" id="tab-products">Products</button>
        </div>
    </div>
    
    <!-- Referrals Section -->
    <div id="section-referrals" class="section-content px-4">
        <div class="app-card p-3 sm:p-4 mb-4">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h2 class="text-base sm:text-lg font-bold" style="color: var(--color-dark);">Your Referrals</h2>
                <a href="{{ route('sponsor.users.create') }}" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-white text-xs sm:text-sm font-semibold" style="background: var(--color-medium);">
                    + Add
                </a>
            </div>
            
            <!-- Search -->
    <form method="GET" action="{{ route('sponsor.dashboard') }}" class="mb-4">
                <div class="relative">
                    <svg class="absolute left-3 top-3 w-5 h-5" style="color: var(--color-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search referrals..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 focus:border-2 focus:outline-none"
                           style="border-color: var(--color-accent); focus:border-color: var(--color-light);">
        </div>
    </form>
    
            @if($referrals->count() > 0)
            <!-- Mobile layout: cards -->
            <div class="space-y-3 md:hidden">
                @foreach($referrals as $referral)
                <x-sponsor.referral-card :referral="$referral" />
                @endforeach
            </div>

            <!-- Desktop layout: table -->
            <div class="hidden md:block mt-4">
                <div class="overflow-x-auto rounded-2xl border-2" style="border-color: var(--color-accent);">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Referral</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Affiliate Code</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Address</th>
                                <th class="px-4 py-2 text-center font-semibold text-gray-700">Orders</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Joined</th>
                                <th class="px-4 py-2 text-right font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($referrals as $referral)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($referral->photo)
                                                <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}"
                                                     class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: var(--color-light);">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-sm" style="color: var(--color-dark);">
                                                    {{ $referral->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-mono" style="color: var(--color-medium);">
                                            {{ $referral->affiliate_code }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($referral->address)
                                            <span class="text-xs" style="color: var(--color-medium);">
                                                {{ $referral->address }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-xs font-medium" style="color: var(--color-dark);">
                                            {{ $referral->orders_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs" style="color: var(--color-medium);">
                                            {{ $referral->created_at->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('sponsor.users.show', $referral) }}"
                                           class="inline-flex items-center px-3 py-1 rounded-full border border-dashed text-xs font-semibold"
                                           style="color: var(--color-medium); border-color: var(--color-medium);">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('sponsor.users.index') }}" class="text-sm font-semibold" style="color: var(--color-medium);">View All Referrals →</a>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-3" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-sm" style="color: var(--color-medium);">No referrals yet</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div id="section-orders" class="section-content px-4 hidden">
        <div class="app-card p-3 sm:p-4 mb-4">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h2 class="text-base sm:text-lg font-bold" style="color: var(--color-dark);">Recent Orders</h2>
                <div class="flex gap-1.5 sm:gap-2">
                    <a href="{{ route('sponsor.orders.my-orders') }}" class="text-[10px] sm:text-xs font-semibold px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg" style="background: var(--color-accent); color: var(--color-dark);">My Orders</a>
                    <a href="{{ route('sponsor.orders.referral-orders') }}" class="text-[10px] sm:text-xs font-semibold px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg text-white" style="background: var(--color-medium);">Referrals</a>
    </div>
</div>

            @if($recentOrders->count() > 0)
            <div class="space-y-3">
                @foreach($recentOrders->take(5) as $order)
                <div class="p-3 rounded-xl border-2" style="border-color: var(--color-accent);">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-mono font-semibold" style="color: var(--color-dark);">#{{ $order->order_number }}</span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ ($order->order_type ?? 'referral_order') === 'my_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ($order->order_type ?? 'referral_order') === 'my_order' ? 'My' : 'Referral' }}
                        </span>
                    </div>
                            <p class="text-xs sm:text-sm font-medium" style="color: var(--color-dark);">{{ $order->product->name }}</p>
                            <p class="text-[10px] sm:text-xs mt-1" style="color: var(--color-medium);">{{ $order->customer_name }}</p>
                </div>
                        <div class="text-right">
                            <p class="text-xs sm:text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</p>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full mt-1 inline-block
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
                    <p class="text-xs mt-2" style="color: var(--color-medium);">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-3" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-sm" style="color: var(--color-medium);">No orders yet</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Referral Links Section -->
    <div id="section-links" class="section-content px-4 hidden">
        <div class="app-card p-3 sm:p-4 mb-4">
            <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4" style="color: var(--color-dark);">General Referral Link</h2>
            
            <div class="mb-4">
                <div class="bg-white rounded-xl p-4 border-2 mb-3 inline-block" style="border-color: var(--color-accent);">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($affiliateLink) }}" 
                         alt="QR Code" class="w-32 h-32">
                </div>
                <button onclick="downloadQRCode()" class="block w-full py-2 rounded-lg text-sm font-semibold text-white mb-3" style="background: var(--color-medium);">
                    Download QR Code
                </button>
</div>

            <div class="flex gap-2">
                <input type="text" value="{{ $affiliateLink }}" readonly 
                       class="flex-1 px-3 py-2.5 rounded-xl border-2 text-xs font-mono focus:outline-none"
                       style="border-color: var(--color-accent);" id="general-link">
                <button onclick="copyLink('general-link')" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white" style="background: var(--color-medium);">
                    Copy
                </button>
            </div>
            <p class="text-xs mt-2" style="color: var(--color-medium);">Share this link to earn commissions on all products</p>
        </div>
    </div>

    <!-- Products Section -->
    <div id="section-products" class="section-content px-4 hidden">
        <div class="app-card p-3 sm:p-4 mb-4">
            <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4" style="color: var(--color-dark);">Product Links</h2>
            
            @if($products->count() > 0)
            <div class="space-y-3 ">
                @foreach($products as $product)
                <div class="p-3 rounded-xl border-2" style="border-color: var(--color-accent); background: var(--color-accent);">
                    <div class="flex items-start gap-3 mb-3">
                    @if($product->main_image)
                            <img src="{{ $product->main_image }}" alt="{{ $product->name }}" 
                                 class="w-16 h-16 rounded-lg object-cover border-2 border-white">
                    @else
                            <div class="w-16 h-16 rounded-lg flex items-center justify-center border-2 border-white" style="background: var(--color-light);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-sm mb-1 truncate" style="color: var(--color-dark);">{{ $product->name }}</h3>
                            <div class="flex gap-2 text-xs">
                                <span class="font-semibold" style="color: var(--color-medium);">৳{{ number_format($product->price, 2) }}</span>
                                @if($product->commission_value)
                                <span class="text-green-600 font-semibold">
                                @if($product->commission_type === 'percentage')
                                    {{ number_format($product->commission_value, 2) }}%
                                @else
                                    ৳{{ number_format($product->commission_value, 2) }}
                                @endif
                                </span>
                                @endif
                        </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" value="{{ route('products.show',$product->slug) }}?ref={{ Auth::user()->affiliate_code }}" 
                               readonly class="flex-1 px-2 py-2 rounded-lg border-2 text-xs font-mono focus:outline-none"
                               style="border-color: white;" id="product-link-{{ $product->id }}">
                        <button onclick="copyLink('product-link-{{ $product->id }}')" 
                                class="px-3 py-2 rounded-lg text-xs font-semibold text-white" style="background: var(--color-medium);">
                            Copy
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-sm" style="color: var(--color-medium);">No active products</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.section-content').forEach(el => el.classList.add('hidden'));
    
    // Remove active class from all tabs
    document.querySelectorAll('.nav-tab').forEach(el => el.classList.remove('active'));
    
    // Show selected section
    document.getElementById('section-' + section).classList.remove('hidden');
    
    // Add active class to selected tab
    document.getElementById('tab-' + section).classList.add('active');
}

function copyLink(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    
    navigator.clipboard.writeText(input.value).then(() => {
        const button = input.nextElementSibling;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.style.background = '#10B981';
        setTimeout(() => {
            button.textContent = originalText;
            button.style.background = '';
        }, 2000);
    }).catch(() => {
        document.execCommand('copy');
        alert('Link copied!');
    });
}

function downloadQRCode() {
    const qrImage = document.querySelector('#section-links img');
    if (qrImage) {
    const link = document.createElement('a');
    link.href = qrImage.src;
    link.download = 'referral-qr-code.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
}
</script>
@endpush
@endsection
