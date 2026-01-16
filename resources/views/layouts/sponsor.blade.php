@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Partner Dashboard') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Noto+Sans+Bengali:wght@100..900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-neutral-50 to-neutral-100 font-sans overflow-x-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024, sidebarCollapsed: false }">
    <div class="min-h-full flex overflow-x-hidden">
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen && window.innerWidth < 1024" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>
        
        <!-- Sidebar -->
        <aside :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            sidebarCollapsed && window.innerWidth >= 1024 ? 'lg:w-20' : 'lg:w-64'
        ]"
               class="fixed h-full overflow-y-auto z-50 transition-all duration-300 ease-in-out w-64 shadow-2xl"
               style="background: linear-gradient(180deg, #0F2854 0%, #1C4D8D 50%, #4988C4 100%); border-right: 1px solid rgba(189, 232, 245, 0.1);">
            <div class="p-4 sm:p-5">
                <div class="flex items-center justify-between mb-6 sm:mb-8">
                    <div x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">
                        <h2 class="text-sm sm:text-base md:text-lg font-bold text-white mb-0.5 sm:mb-1">Partner</h2>
                        <p class="text-[10px] sm:text-xs text-white/70">Dashboard</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition">
                            <svg x-show="!sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                            <svg x-show="sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <button @click="sidebarOpen = false" class="lg:hidden text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('sponsor.dashboard') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Dashboard' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-xl {{ request()->routeIs('sponsor.dashboard') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-xs sm:text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.dashboard') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Dashboard</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Dashboard</span>
                    </a>
                    <a href="{{ route('sponsor.orders.referral-orders') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Orders' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sponsor.orders.*') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.orders.*') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Orders</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Orders</span>
                    </a>
                    <a href="{{ route('sponsor.earnings.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Earnings' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sponsor.earnings.*') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.earnings.*') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Earnings</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Earnings</span>
                    </a>
                    <a href="{{ route('sponsor.withdrawals.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Withdrawals' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sponsor.withdrawals.*') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.withdrawals.*') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 11h10M4 15h7M4 19h16"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Withdrawals</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Withdrawals</span>
                    </a>
                    <a href="{{ route('sponsor.withdrawal-methods') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Withdrawal Methods' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sponsor.withdrawal-methods') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.withdrawal-methods') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Withdrawal Methods</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Withdrawal Methods</span>
                    </a>
                    <a href="{{ route('sponsor.users.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'My Referrals' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sponsor.users.index') || request()->routeIs('sponsor.users.show') || request()->routeIs('sponsor.users.edit') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.users.index') || request()->routeIs('sponsor.users.show') || request()->routeIs('sponsor.users.edit') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">My Referrals</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">My Referrals</span>
                    </a>
                    <a href="{{ route('sponsor.users.create') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Add Referral' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sponsor.users.create') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.users.create') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Add Referral</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Add Referral</span>
                    </a>
                    <a href="{{ route('sponsor.profile.edit') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Edit Profile' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sponsor.profile.*') ? 'bg-white shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''"
                       style="{{ request()->routeIs('sponsor.profile.*') ? 'color: #0F2854;' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Edit Profile</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Edit Profile</span>
                    </a>
                    <a href="{{ route('home') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'View Site' : ''"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/10 transition-all group relative text-sm font-medium"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">View Site</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">View Site</span>
                    </a>
                    <div class="pt-4 mt-4 border-t" style="border-color: rgba(189, 232, 245, 0.2);">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Logout' : ''"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-red-500/20 hover:text-white transition-all group relative text-sm font-medium"
                                    :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center px-3' : ''">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Logout</span>
                                <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl">Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'lg:ml-20' : 'lg:ml-64'" class="flex-1 transition-all duration-300 min-w-0 overflow-x-hidden">
            <!-- Top Bar -->
            <div class="bg-white/95 backdrop-blur-sm shadow-sm border-b sticky top-0 z-30" style="border-color: rgba(189, 232, 245, 0.3);">
                <div class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 flex items-center justify-between">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 rounded-lg transition" style="color: #0F2854;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-sm sm:text-base md:text-lg lg:text-xl font-bold truncate flex-1 ml-2 sm:ml-0" style="color: #0F2854;">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center gap-2 sm:gap-3">
                        @php
                            $income = Auth::user()->balance;
                        @endphp
                        <div class="hidden sm:flex items-center gap-2 rounded-xl px-3 py-2 shadow-sm" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm font-semibold text-white">৳{{ number_format($income, 2) }}</span>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-full transition"
                            >
                                @if(Auth::user()->photo)
                                    <img src="{{ Storage::disk('public')->url(Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border-2 transition-colors" style="border-color: #BDE8F5;">
                                @else
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center border-2 transition-colors" style="background: #4988C4; border-color: #BDE8F5;">
                                        <span class="text-white font-semibold text-xs sm:text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </button>
                            <div 
                                x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-neutral-200 py-1 z-50"
                                style="display: none;"
                            >
                                <div class="px-4 py-3 border-b border-neutral-100">
                                    <p class="text-sm font-semibold text-neutral-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-neutral-500 truncate">{{ Auth::user()->affiliate_code }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-3 sm:p-4 lg:p-8 max-w-full overflow-x-hidden">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
