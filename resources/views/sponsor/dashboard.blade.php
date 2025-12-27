@extends('layouts.sponsor')

@section('title', 'Partner Dashboard')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Profile Card -->
    <div class="bg-indigo-500 rounded-2xl shadow-xl overflow-hidden border border-neutral-100 h-full">
        <div class="bg-indigo-500 p-4 sm:p-5 md:p-6">
            <!-- Mobile Layout: Stacked -->
            <div class="flex flex-col md:hidden gap-4">
                <!-- Photo and Name Row -->
                <div class="flex items-center gap-4">
                    <div class="relative flex-shrink-0">
                        @if(Auth::user()->photo)
                            <img src="{{ Storage::disk('public')->url(Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-lg ring-2 ring-primary/20">
                        @else
                            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center border-2 border-white shadow-lg ring-2 ring-primary/20">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 bg-green-500 border-2 border-white rounded-full w-4 h-4"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-bold text-white truncate">{{ Auth::user()->name }}</h1>
                        <small class="text-white text-sm">{{ Auth::user()->affiliate_code }}</small>
                    </div>
                </div>
                <!-- Badges Row -->
                
                <!-- Edit Button -->
                <a href="{{ route('sponsor.profile.edit') }}" class="group bg-white text-indigo-600 px-4 py-3 rounded-xl hover:bg-indigo-50 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2 w-full border-2 border-white hover:border-indigo-200">
                    <svg class="w-4 h-4 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span class="text-sm font-bold">Edit Profile</span>
                </a>
            </div>

            <!-- Desktop Layout: Horizontal -->
            <div class="hidden md:flex md:items-center md:justify-between gap-4 lg:gap-6">
                <div class="flex items-center gap-4 lg:gap-6 flex-1 min-w-0">
                    <!-- User Photo -->
                    <div class="relative flex-shrink-0">
                        @if(Auth::user()->photo)
                            <img src="{{ Storage::disk('public')->url(Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="w-20 h-20 lg:w-24 lg:h-24 rounded-full object-cover border-2 lg:border-4 border-white shadow-lg ring-2 lg:ring-4 ring-primary/20">
                        @else
                            <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-full bg-white flex items-center justify-center border-2 lg:border-4 border-white shadow-lg ring-2 lg:ring-4 ring-primary/20">
                                <svg class="w-10 h-10 lg:w-12 lg:h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 bg-green-500 border-2 lg:border-4 border-white rounded-full w-5 h-5 lg:w-6 lg:h-6"></div>
                    </div>
                    <!-- User Info -->
                    <div class="text-white flex-1 min-w-0">
                        <h1 class="text-2xl lg:text-3xl font-bold mb-2 truncate">{{ Auth::user()->name }}</h1>
                        <div class="flex flex-wrap items-center gap-2 lg:gap-4">
                            <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="text-xs lg:text-sm font-medium">Partner Code</span>
                                <span class="font-mono font-bold text-sm lg:text-lg">{{ Auth::user()->affiliate_code }}</span>
                            </div>
                            @if(Auth::user()->phone)
                            <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-xs lg:text-sm font-medium truncate">{{ Auth::user()->phone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Edit Button -->
                <a href="{{ route('sponsor.profile.edit') }}" class="group bg-white text-indigo-600 px-5 lg:px-6 py-2.5 lg:py-3 rounded-xl hover:bg-indigo-50 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center gap-2 transform hover:scale-105 flex-shrink-0 border-2 border-white hover:border-indigo-200">
                    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span class="text-sm lg:text-base font-bold">Edit Profile</span>
    </a>
</div>
        </div>
    </div>

    <!-- Income Card -->
    <div class="bg-green-500 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition duration-200 flex flex-col justify-between">
    <div class="flex items-center justify-between mb-4">
            <div class="bg-green-600 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div>
            <h3 class="text-green-100 text-sm font-medium mb-2">Income</h3>
            <p class="text-4xl md:text-5xl font-bold">৳0</p>
        </div>
    </div>
</div>
  <!-- General Partner Link -->
  <div class="bg-blue-50 rounded-xl shadow-lg p-6 mb-8 border border-blue-100">
    <div class="flex items-center gap-3 mb-4">
        <div class="bg-blue-500 rounded-lg p-2">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
    </div>
        <h2 class="text-xl font-bold text-blue-900"> Referral Link</h2>
    </div>
    <div class="flex gap-2">
        <input type="text" value="{{ $affiliateLink }}" readonly class="flex-1 px-4 py-3 border-2 border-blue-200 rounded-lg bg-white font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="general-partner-link">
        <button onclick="copyPartnerLink('general-partner-link')" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold shadow-md hover:shadow-lg">
            Copy Link
        </button>
    </div>
    <p class="text-sm text-blue-700 mt-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Share this link to earn commissions on all products!
    </p>
</div>

<div class="bg-white rounded-xl shadow-lg p-6 border border-neutral-100">
    <div class="flex flex-col sm:flex-row md:flex-row gap-2 items-center justify-between mb-4">
        <div class="flex items-center gap-3">
        <div class="bg-indigo-500 rounded-lg p-2">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-neutral-900">Your Referrals</h2>
   
        </div>
        
   
        <a 
            href="{{ route('sponsor.users.create') }}" 
            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-md hover:shadow-lg transform hover:scale-105"
        >
            + Add  New Referral
        </a>
    </div>
    
    <!-- Search Form -->
    <form method="GET" action="{{ route('sponsor.dashboard') }}" class="mb-4">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-3 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                        <input 
                            type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by name, phone, address, or affiliate code..."
                    class="w-full pl-10 pr-4 py-2 border-2 border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-md hover:shadow-lg">
                Search
                        </button>
            @if(request('search'))
            <a href="{{ route('sponsor.dashboard') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold">
                Clear
            </a>
            @endif
        </div>
    </form>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Affiliate Code</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-indigo-900 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($referrals as $referral)
                <tr class="hover:bg-indigo-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($referral->photo)
                            <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-neutral-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('sponsor.users.show', $referral) }}" class="text-primary hover:underline">{{ $referral->name }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $referral->phone ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-500 max-w-xs truncate" title="{{ $referral->address ?? 'N/A' }}">
                        {{ $referral->address ? Str::limit($referral->address, 30) : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 font-mono">{{ $referral->affiliate_code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $referral->orders_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $referral->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('sponsor.users.show', $referral) }}" class="text-primary hover:text-primary-light font-medium">View</a>
                            <a href="{{ route('sponsor.users.edit', $referral) }}" class="text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-neutral-500">
                        @if(request('search'))
                            No referrals found matching "{{ request('search') }}"
                        @else
                            No referrals yet
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


</div>
<br>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Total Referrals Card -->
    <div class="bg-blue-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-600 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-blue-100 text-sm font-medium mb-2">Total Referrals</h3>
        <p class="text-4xl font-bold">{{ $stats['total_referrals'] }}</p>
    </div>

    <!-- Total Orders Card -->
    <div class="bg-purple-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-purple-600 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                    </div>
                </div>
        <h3 class="text-purple-100 text-sm font-medium mb-2">Total Orders</h3>
        <p class="text-4xl font-bold">{{ $stats['total_orders'] }}</p>
    </div>

    <!-- Pending Orders Card -->
    <div class="bg-orange-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-orange-600 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-orange-100 text-sm font-medium mb-2">Pending Orders</h3>
        <p class="text-4xl font-bold">{{ $stats['pending_orders'] }}</p>
    </div>

    <!-- Income Card -->
    
</div>





<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-neutral-100">
    <div class="flex items-center gap-3 mb-4">
        <div class="bg-orange-500 rounded-lg p-2">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-neutral-900">Recent Orders</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-orange-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-orange-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-neutral-900">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-700">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-700">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-600">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-neutral-500">No orders yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Referrals -->

<br>
  

<!-- Product-Specific Partner Links -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-neutral-100">
    <div class="flex items-center gap-3 mb-4">
        <div class="bg-purple-500 rounded-lg p-2">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-neutral-900">Product Referral Links</h2>
    </div>
    <p class="text-sm text-neutral-600 mb-4">Share these product-specific partner links to track commissions for each product.</p>
    
    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
        @forelse($products as $product)
        <div class="border-2 border-purple-100 rounded-lg p-4 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h3 class="font-semibold text-neutral-900 mb-1">{{ $product->name }}</h3>
                    <p class="text-xs text-purple-600 font-medium mb-2">Price: ৳{{ number_format($product->price, 2) }}</p>
                    <div class="flex gap-2 items-center">
                        <input 
                            type="text" 
                            value="{{ route('products.show', $product->slug) }}?ref={{ Auth::user()->affiliate_code }}" 
                            readonly 
                            class="flex-1 px-3 py-2 text-xs border-2 border-purple-200 rounded-lg bg-white font-mono focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            id="partner-link-{{ $product->id }}"
                        >
                        <button 
                            onclick="copyPartnerLink('partner-link-{{ $product->id }}')" 
                            class="px-4 py-2 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition font-semibold shadow-md hover:shadow-lg whitespace-nowrap"
                        >
                            Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
                @empty
        <p class="text-neutral-500 text-center py-8">No active products found</p>
                @endforelse
    </div>
</div>
@push('scripts')
<script>
function copyPartnerLink(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(input.value).then(function() {
        // Show temporary success message
        const button = input.nextElementSibling;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-green-600');
        button.classList.remove('bg-primary', 'hover:bg-primary-light');
        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-primary', 'hover:bg-primary-light');
        }, 2000);
    }).catch(function(err) {
        // Fallback for older browsers
        document.execCommand('copy');
        alert('Partner link copied to clipboard!');
    });
}

</script>
@endpush
@endsection


