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
               class="bg-gradient-to-b from-primary to-primary-light shadow-xl border-r border-primary/20 fixed h-full overflow-y-auto z-50 transition-all duration-300 ease-in-out w-64">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 x-show="!sidebarCollapsed || window.innerWidth < 1024" class="text-lg sm:text-xl font-bold text-white transition-opacity duration-300">Partner Panel</h2>
                    <div class="flex gap-2">
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex text-white hover:text-neutral-200 hover:bg-white/10 p-2 rounded-lg transition">
                            <svg x-show="!sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                            <svg x-show="sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-neutral-200 p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <nav class="space-y-1 sm:space-y-2">
                    <a href="{{ route('sponsor.dashboard') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Dashboard' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('sponsor.dashboard') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Dashboard</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Dashboard</span>
                    </a>
                    <a href="{{ route('sponsor.orders.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Orders' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('sponsor.orders.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Orders</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Orders</span>
                    </a>
                    <a href="{{ route('sponsor.users.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'My Referrals' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('sponsor.users.index') || request()->routeIs('sponsor.users.show') || request()->routeIs('sponsor.users.edit') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">My Referrals</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">My Referrals</span>
                    </a>
                    <a href="{{ route('sponsor.users.create') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Add Referral' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('sponsor.users.create') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Add Referral</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Add Referral</span>
                    </a>
                    <a href="{{ route('sponsor.profile.edit') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Edit Profile' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('sponsor.profile.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Edit Profile</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Edit Profile</span>
                    </a>
                    <a href="{{ route('home') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'View Site' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-white/90 hover:bg-white/10 transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">View Site</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">View Site</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" 
                                :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Logout' : ''"
                                class="w-full flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-white/90 hover:bg-red-500/20 transition group relative text-sm sm:text-base"
                                :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Logout</span>
                            <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Logout</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'lg:ml-20' : 'lg:ml-64'" class="flex-1 transition-all duration-300 min-w-0 overflow-x-hidden">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm border-b border-neutral-200 sticky top-0 z-30">
                <div class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 flex items-center justify-between">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-neutral-700 hover:text-primary p-2 -ml-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-neutral-800 truncate flex-1 ml-2 sm:ml-0">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center gap-2 sm:gap-3">
                        @php
                            $income = Auth::user()->orders()->where('status', '!=', 'cancelled')->sum('total_price');
                        @endphp
                        <div class="hidden sm:flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-3 py-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm font-semibold text-green-700">৳{{ number_format($income, 2) }}</span>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-full transition"
                            >
                                @if(Auth::user()->photo)
                                    <img src="{{ Storage::disk('public')->url(Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-neutral-200 hover:border-primary transition-colors">
                                @else
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary flex items-center justify-center border-2 border-neutral-200 hover:border-primary transition-colors">
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
