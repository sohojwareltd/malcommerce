                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           @extends('layouts.sponsor')

@section('title', 'Partner Dashboard')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="mb-4 sm:mb-6 lg:mb-8 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
    <!-- Profile Card -->
    <div class="bg-primary rounded-xl sm:rounded-2xl shadow-xl overflow-hidden border border-neutral-100 h-full">
        <div class="bg-primary p-3 sm:p-4 md:p-6">
            <!-- Mobile Layout: Stacked -->
            <div class="flex flex-col md:hidden gap-3">
                <!-- Photo and Name Row -->
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        @if(Auth::user()->photo)
                            <img src="{{ Storage::disk('public')->url(Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-lg">
                        @else
                            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center border-2 border-white shadow-lg">
                                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 bg-green-500 border-2 border-white rounded-full w-3.5 h-3.5"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg font-bold text-white truncate">{{ Auth::user()->name }}</h1>
                        <small class="text-white/90 text-xs font-mono">{{ Auth::user()->affiliate_code }}</small>
                    </div>
                </div>
                <!-- Edit Button -->
                <a href="{{ route('sponsor.profile.edit') }}" class="group bg-white text-primary px-3 py-2.5 rounded-lg hover:bg-primary/10 transition-all duration-200 font-semibold shadow-lg flex items-center justify-center gap-2 w-full">
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
                        <h1 class="text-2xl lg:text-3xl text-white font-bold mb-2 truncate">{{ Auth::user()->name }}</h1>
                        <div class="flex flex-wrap items-center gap-2 lg:gap-4">
                            <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
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
                <a href="{{ route('sponsor.profile.edit') }}" class="group bg-white text-primary px-5 lg:px-6 py-2.5 lg:py-3 rounded-xl hover:bg-primary/10 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center gap-2 transform hover:scale-105 flex-shrink-0 border-2 border-white hover:border-primary/20">
                    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span class="text-sm lg:text-base font-bold">Edit Profile</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Income Card -->
    <div class="bg-green-500 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 text-white transform hover:scale-105 transition duration-200 flex flex-col justify-between min-w-0">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="bg-green-600 rounded-lg p-2 sm:p-3">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div>
            <h3 class="text-green-100 text-xs sm:text-sm font-medium mb-1 sm:mb-2">Income</h3>
            <p class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold truncate">৳{{ number_format(Auth::user()->balance ?? 0, 2) }}</p>
        </div>
    </div>
</div>
  <!-- General Partner Link -->
  <div class="bg-blue-50 rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6 lg:mb-8 border border-blue-100">
    <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
        <div class="bg-blue-500 rounded-lg p-1.5 sm:p-2">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
        </div>
        <h2 class="text-lg sm:text-xl font-bold text-blue-900">Referral Link</h2>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        <input type="text" value="{{ $affiliateLink }}" readonly class="flex-1 px-3 sm:px-4 py-2 sm:py-3 border-2 border-blue-200 rounded-lg bg-white font-mono text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 truncate" id="general-partner-link">
        <button onclick="copyPartnerLink('general-partner-link')" class="bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-700 transition font-semibold shadow-md hover:shadow-lg text-sm sm:text-base whitespace-nowrap">
            Copy Link
        </button>
    </div>
    <p class="text-xs sm:text-sm text-blue-700 mt-2 sm:mt-3 flex items-start gap-2">
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Share this link to earn commissions on all products!</span>
    </p>
</div>

<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-neutral-100 overflow-x-hidden">
    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-start sm:items-center justify-between mb-4">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="bg-primary rounded-lg p-1.5 sm:p-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Your Referrals</h2>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
            <a 
                href="{{ route('sponsor.users.index') }}" 
                class="bg-neutral-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-neutral-700 transition font-semibold shadow-md hover:shadow-lg transform hover:scale-105 text-sm sm:text-base text-center flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                View All Referrals
            </a>
            <a 
                href="{{ route('sponsor.users.create') }}" 
                class="bg-primary text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-primary-light transition font-semibold shadow-md hover:shadow-lg transform hover:scale-105 text-sm sm:text-base text-center"
            >
                + Add New Referral
            </a>
        </div>
    </div>
    
    <!-- Search Form -->
    <form method="GET" action="{{ route('sponsor.dashboard') }}" class="mb-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-2.5 sm:top-3 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by name, phone, address, or affiliate code..."
                    class="w-full pl-10 pr-4 py-2 border-2 border-primary/20 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base"
                >
            </div>
            <button type="submit" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold shadow-md hover:shadow-lg text-sm sm:text-base">
                Search
            </button>
            @if(request('search'))
            <a href="{{ route('sponsor.dashboard') }}" class="bg-neutral-200 text-neutral-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base text-center">
                Clear
            </a>
            @endif
        </div>
    </form>
    
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-primary/10">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Affiliate Code</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-primary uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($referrals as $referral)
                <tr class="hover:bg-primary/5 transition-colors">
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
    
    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($referrals as $referral)
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 hover:shadow-md transition-shadow overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-primary/10 to-primary/20 px-4 py-3 border-b border-primary/20">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        @if($referral->photo)
                            <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm">
                        @else
                            <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center border-2 border-white shadow-sm">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-neutral-900 truncate mb-0.5">{{ $referral->name }}</h3>
                        <p class="text-xs text-primary font-mono font-semibold">{{ $referral->affiliate_code }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Content Section -->
            <div class="p-4">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-neutral-50 rounded-lg p-2.5">
                        <p class="text-xs text-neutral-500 mb-1">Phone</p>
                        <p class="text-sm font-semibold text-neutral-900 truncate">{{ $referral->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-neutral-50 rounded-lg p-2.5">
                        <p class="text-xs text-neutral-500 mb-1">Orders</p>
                        <p class="text-sm font-semibold text-neutral-900">{{ $referral->orders_count }}</p>
                    </div>
                </div>
                
                <!-- Joined Date -->
                <div class="mb-3 pb-3 border-b border-neutral-100">
                    <p class="text-xs text-neutral-500 mb-0.5">Joined</p>
                    <p class="text-sm font-medium text-neutral-700">{{ $referral->created_at->format('M d, Y') }}</p>
                </div>
                
                <!-- Address (if available) -->
                @if($referral->address)
                <div class="mb-3">
                    <p class="text-xs text-neutral-500 mb-1">Address</p>
                    <p class="text-xs text-neutral-700 line-clamp-2">{{ $referral->address }}</p>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('sponsor.users.show', $referral) }}" class="flex-1 bg-primary text-white text-center px-4 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold text-sm shadow-sm">
                        View
                    </a>
                    <a href="{{ route('sponsor.users.edit', $referral) }}" class="flex-1 bg-white text-primary text-center px-4 py-2.5 rounded-lg hover:bg-primary/10 transition font-semibold text-sm border-2 border-primary/20">
                        Edit
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">
            @if(request('search'))
                No referrals found matching "{{ request('search') }}"
            @else
                No referrals yet
            @endif
        </div>
        @endforelse
    </div>
</div>
<br>

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
    <!-- Total Referrals Card -->
    <div class="bg-blue-500 rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 text-white transform hover:scale-105 transition duration-200 min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-blue-600 rounded-lg p-1.5 sm:p-2 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-blue-100 text-[10px] sm:text-xs lg:text-sm font-medium mb-0.5 sm:mb-1 lg:mb-2 truncate">Total Referrals</h3>
        <p class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-bold truncate">{{ $stats['total_referrals'] }}</p>
    </div>

    <!-- Total Orders Card -->
    <div class="bg-purple-500 rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 text-white transform hover:scale-105 transition duration-200 min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-purple-600 rounded-lg p-1.5 sm:p-2 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-purple-100 text-[10px] sm:text-xs lg:text-sm font-medium mb-0.5 sm:mb-1 lg:mb-2 truncate">Total Orders</h3>
        <p class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-bold truncate">{{ $stats['total_orders'] }}</p>
    </div>

    <!-- Pending Orders Card -->
    <div class="bg-orange-500 rounded-xl shadow-lg p-3 sm:p-4 lg:p-6 text-white transform hover:scale-105 transition duration-200 min-w-0">
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
            <div class="bg-orange-600 rounded-lg p-1.5 sm:p-2 lg:p-3 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-orange-100 text-[10px] sm:text-xs lg:text-sm font-medium mb-0.5 sm:mb-1 lg:mb-2 truncate">Pending Orders</h3>
        <p class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-bold truncate">{{ $stats['pending_orders'] }}</p>
    </div>
</div>





<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6 lg:mb-8 border border-neutral-100 overflow-x-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-3 sm:mb-4">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="bg-orange-500 rounded-lg p-1.5 sm:p-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Recent Orders</h2>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('sponsor.orders.my-orders') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold text-sm text-center">
                View My Orders
            </a>
            <a href="{{ route('sponsor.orders.referral-orders') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition font-semibold text-sm text-center">
                View Referral Orders
            </a>
        </div>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-orange-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-900 uppercase">Type</th>
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
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ ($order->order_type ?? 'referral_order') === 'my_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ($order->order_type ?? 'referral_order') === 'my_order' ? 'My Order' : 'Referral Order' }}
                        </span>
                    </td>
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
                    <td colspan="7" class="px-6 py-4 text-center text-neutral-500">No orders yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($recentOrders as $order)
        <div class="bg-neutral-50 rounded-lg p-3 border border-neutral-200">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono text-neutral-500">#</span>
                        <span class="text-sm font-semibold text-neutral-900 truncate">{{ $order->order_number }}</span>
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ ($order->order_type ?? 'referral_order') === 'my_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ($order->order_type ?? 'referral_order') === 'my_order' ? 'My Order' : 'Referral' }}
                        </span>
                    </div>
                    <p class="text-xs text-neutral-500">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex flex-col items-end gap-2 ml-2">
                    <span class="text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</span>
                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                           'bg-blue-100 text-blue-800') }}">
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
            </div>
        </div>
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

<!-- Referrals -->

<br>
  

<!-- Product-Specific Partner Links -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6 lg:mb-8 border border-neutral-100 overflow-x-hidden">
    <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
        <div class="bg-purple-500 rounded-lg p-1.5 sm:p-2">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
        </div>
        <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Product Referral Links</h2>
    </div>
    <p class="text-xs sm:text-sm text-neutral-600 mb-3 sm:mb-4">Share these product-specific partner links to track commissions for each product.</p>
    
    <div class="space-y-3 max-h-96 overflow-y-auto pr-1 sm:pr-2">
        @forelse($products as $product)
        <div class="border-2 border-purple-100 rounded-lg p-3 sm:p-4 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200">
            <div class="flex items-start gap-3 sm:gap-4">
                <!-- Product Image -->
                <div class="flex-shrink-0">
                    @if($product->main_image)
                        <img src="{{ Storage::disk('public')->url($product->main_image) }}" alt="{{ $product->name }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover border-2 border-purple-200 shadow-sm">
                    @else
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg bg-purple-100 border-2 border-purple-200 flex items-center justify-center">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Product Info and Link -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm sm:text-base text-neutral-900 mb-2 truncate">{{ $product->name }}</h3>
                    
                    <!-- Product Details Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                        <div class="bg-white rounded-lg p-2 border border-purple-100">
                            <p class="text-xs text-neutral-500 mb-0.5">Price</p>
                            <p class="text-sm font-bold text-purple-600">৳{{ number_format($product->price, 2) }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-2 border border-purple-100">
                            <p class="text-xs text-neutral-500 mb-0.5">Commission</p>
                            <p class="text-sm font-bold text-green-600">
                                @if($product->commission_type === 'percentage')
                                    {{ number_format($product->commission_value, 2) }}%
                                @else
                                    ৳{{ number_format($product->commission_value, 2) }}
                                @endif
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-2 border border-purple-100">
                            <p class="text-xs text-neutral-500 mb-0.5">Cashback</p>
                            <p class="text-sm font-bold text-blue-600">৳{{ number_format($product->cashback_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-2 border border-purple-100 sm:col-span-1">
                            <p class="text-xs text-neutral-500 mb-0.5">Total Earn</p>
                            <p class="text-sm font-bold text-orange-600">
                                @php
                                    $commission = $product->commission_type === 'percentage' 
                                        ? ($product->price * $product->commission_value / 100)
                                        : $product->commission_value;
                                    $totalEarn = $commission + ($product->cashback_amount ?? 0);
                                @endphp
                                ৳{{ number_format($totalEarn, 2) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Affiliate Link -->
                    <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                        <input 
                            type="text" 
                            value="{{ route('products.show', $product->slug) }}?ref={{ Auth::user()->affiliate_code }}" 
                            readonly 
                            class="flex-1 px-2 sm:px-3 py-2 text-xs border-2 border-purple-200 rounded-lg bg-white font-mono focus:ring-2 focus:ring-purple-500 focus:border-purple-500 truncate"
                            id="partner-link-{{ $product->id }}"
                        >
                        <button 
                            onclick="copyPartnerLink('partner-link-{{ $product->id }}')" 
                            class="px-3 sm:px-4 py-2 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition font-semibold shadow-md hover:shadow-lg whitespace-nowrap"
                        >
                            Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-neutral-500 text-center py-8 text-sm">No active products found</p>
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


