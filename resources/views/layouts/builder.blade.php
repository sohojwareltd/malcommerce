<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Page Builder') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Noto+Sans+Bengali:wght@100..900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    
    @stack('styles')
</head>
<body class="h-full bg-[#F6F6F7] font-sans overflow-hidden" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <!-- Top Bar - Shopify Style -->
    <div class="bg-white border-b border-[#E1E3E5] sticky top-0 z-50">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-[#637381] hover:text-[#202223] p-1.5 rounded hover:bg-[#F6F6F7] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="h-5 w-px bg-[#E1E3E5]"></div>
                <div>
                    <h1 class="text-base font-semibold text-[#202223] leading-tight">@yield('title', 'Page Builder')</h1>
                    @hasSection('subtitle')
                        <p class="text-xs text-[#637381] mt-0.5">@yield('subtitle')</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                @hasSection('header-actions')
                    @yield('header-actions')
                @endif
            </div>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="h-[calc(100vh-57px)] overflow-hidden">
        @yield('content')
    </div>
    
    <!-- jQuery (required for Slick) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Slick JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    
    @stack('scripts')
</body>
</html>

