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
<body class="h-full bg-neutral-100 font-sans">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 bg-white shadow-sm border-b border-neutral-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-4">
                        <!-- Home Icon -->
                        <a href="{{ route('sponsor.dashboard') }}" class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-neutral-100 transition-colors text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </a>
                        
                        <!-- Income Badge -->
                        @php
                            $income = Auth::user()->orders()->where('status', '!=', 'cancelled')->sum('total_price');
                        @endphp
                        <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-3 py-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-green-700">৳{{ number_format($income, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Web Icon -->
                        <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-neutral-100 transition-colors text-neutral-600 hover:text-primary" title="View Website">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </a>
                        <!-- User Avatar with Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button 
                            @click="open = !open"
                            class="flex items-center focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-full transition"
                        >
                            @if(Auth::user()->photo)
                                <img src="{{ Storage::disk('public')->url(Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-neutral-200 hover:border-primary transition-colors">
                            @else
                                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center border-2 border-neutral-200 hover:border-primary transition-colors">
                                    <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </button>
                        <!-- Dropdown Menu -->
                        <div 
                            x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-60 bg-white rounded-lg shadow-lg border border-neutral-200 py-1 z-50"
                            style="display: none;"
                        >
                            <div class="px-4 py-2 border-b border-neutral-100">
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
        </nav>
        
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-4 bg-accent/10 border border-accent text-accent px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>


