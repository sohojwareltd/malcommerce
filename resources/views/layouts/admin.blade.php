<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>
    
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
                    <h2 x-show="!sidebarCollapsed || window.innerWidth < 1024" class="text-lg sm:text-xl font-bold text-white transition-opacity duration-300">Admin Panel</h2>
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
                    <a href="{{ route('admin.dashboard') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Dashboard' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Dashboard</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.products.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Products' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Products</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Products</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Categories' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Categories</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Categories</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Orders' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Orders</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Orders</span>
                    </a>
                    <a href="{{ route('admin.sponsors.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Sponsors' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.sponsors.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Sponsors</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Sponsors</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Admin Users' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Admin Users</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Admin Users</span>
                    </a>
                    <a href="{{ route('admin.reports.sales') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Sales Report' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Sales Report</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Sales Report</span>
                    </a>
                    <a href="{{ route('admin.profile.edit') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Profile' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.profile.*') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Profile</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Profile</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" 
                       :title="sidebarCollapsed && window.innerWidth >= 1024 ? 'Settings' : ''"
                       class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-white text-primary shadow-lg' : 'text-white/90 hover:bg-white/10' }} transition group relative text-sm sm:text-base"
                       :class="sidebarCollapsed && window.innerWidth >= 1024 ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="transition-opacity duration-300">Settings</span>
                        <span x-show="sidebarCollapsed && window.innerWidth >= 1024" class="absolute left-full ml-2 px-2 py-1 bg-neutral-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50">Settings</span>
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
                    <div class="w-6 sm:w-6"></div>
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

