<!DOCTYPE html>
<html lang="bn" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        use Illuminate\Support\Facades\Storage;
        $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Shop'));
        $canonicalUrl = url()->current();
        
        // Get OG image from settings, or use a default placeholder
        $ogImageSetting = \App\Models\Setting::get('og_image');
        if ($ogImageSetting) {
            // If it's a full URL, use it; otherwise make it absolute
            if (filter_var($ogImageSetting, FILTER_VALIDATE_URL)) {
                $ogImage = $ogImageSetting;
            } else {
                // Make relative path absolute
                $ogImage = strpos($ogImageSetting, '/') === 0 
                    ? url($ogImageSetting) 
                    : url('/' . $ogImageSetting);
            }
        } else {
            // No OG image set - we'll conditionally include og:image tag only if we have a valid image
            $ogImage = null;
        }
    @endphp
    
    <!-- Primary Meta Tags -->
    @php
        $metaDesc = \App\Models\Setting::get('meta_description', 'আমাদের অনলাইন শপ থেকে সেরা পণ্য কিনুন। ফ্রি ডেলিভারি ও ক্যাশ অন ডেলিভারি সুবিধা।');
        // Allow views to override meta description
        $metaDesc = $metaDescOverride ?? $metaDesc;
        $ogImageOverride = $ogImageOverride ?? null;
        $ogType = $ogTypeOverride ?? 'website';
    @endphp
    <title>{{ $siteName }} - @yield('title', 'হোম')</title>
    <meta name="title" content="{{ $siteName }} - @yield('title', 'হোম')">
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="keywords" content="@yield('keywords', 'অনলাইন শপ, ই-কমার্স, পণ্য, ডেলিভারি')">
    <meta name="author" content="{{ $siteName }}">
    <meta name="language" content="bn">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $siteName }} - @yield('title', 'হোম')">
    <meta property="og:description" content="{{ $metaDesc }}">
    @php
        $finalOgImage = $ogImageOverride ?: $ogImage;
    @endphp
    @if($finalOgImage && filter_var($finalOgImage, FILTER_VALIDATE_URL))
    <meta property="og:image" content="{{ $finalOgImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:alt" content="{{ $siteName }} - @yield('title', 'হোম')">
    @endif
    <meta property="og:locale" content="bn_BD">
    <meta property="og:site_name" content="{{ $siteName }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $canonicalUrl }}">
    <meta property="twitter:title" content="{{ $siteName }} - @yield('title', 'হোম')">
    <meta property="twitter:description" content="{{ $metaDesc }}">
    @if($finalOgImage && filter_var($finalOgImage, FILTER_VALIDATE_URL))
    <meta property="twitter:image" content="{{ $finalOgImage }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&family=Noto+Sans+Bengali:wght@100..900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    @php
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => $siteName,
            "url" => url('/'),
            "logo" => $ogImage,
            "description" => $metaDesc,
            "address" => [
                "@type" => "PostalAddress",
                "addressCountry" => "BD"
            ],
            "contactPoint" => [
                "@type" => "ContactPoint",
                "telephone" => \App\Models\Setting::get('contact_phone', ''),
                "contactType" => "Customer Service",
                "areaServed" => "BD",
                "availableLanguage" => "bn"
            ]
        ];
    @endphp
    {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
        
    <!-- Settings: FB Pixel & GA -->
    @if(\App\Models\Setting::get('fb_pixel_id'))
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ \App\Models\Setting::get("fb_pixel_id") }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ \App\Models\Setting::get('fb_pixel_id') }}&ev=PageView&noscript=1"
    /></noscript>
    @endif
    
    @if(\App\Models\Setting::get('ga_tracking_id'))
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('ga_tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ \App\Models\Setting::get("ga_tracking_id") }}');
    </script>
    @endif
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    
    @stack('styles')
    @stack('head')
    
    <!-- Dynamic Color Injection from Settings -->
    <style>
        :root {
            @php
                $colorPalette = json_decode(\App\Models\Setting::get('color_palette', '{}'), true);
                $primaryColor = $colorPalette['primary'] ?? '#2563EB';
                $secondaryColor = $colorPalette['secondary'] ?? '#64748B';
                $accentColor = $colorPalette['accent'] ?? '#10B981';
            @endphp
            --color-primary: {{ $primaryColor }};
            --color-secondary: {{ $secondaryColor }};
            --color-accent: {{ $accentColor }};
        }
    </style>
</head>
<body class="h-full bg-white font-sans" style="font-family: var(--font-family-base, 'Raleway', ui-sans-serif, system-ui, sans-serif);">
    <div class="min-h-full flex flex-col">
        <!-- Marquee Banner -->
        @php
            $marqueeText = \App\Models\Setting::get('marquee_text', '');
        @endphp
        @if($marqueeText)
            <div class="bg-primary text-white py-2 overflow-hidden" style="background-color: var(--color-primary);">
                <div class="marquee-container">
                    <div class="marquee-content font-bangla text-sm font-medium whitespace-nowrap">
                        {{ $marqueeText }}
                    </div>
                </div>
            </div>
        @endif
            
        @unless(isset($hideLayoutChrome) && $hideLayoutChrome)
        <!-- Header -->
        <header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
            @php
                $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();
            @endphp
            
            <!-- Top Bar -->
           
            
            <!-- Main Navigation -->
            <nav class="bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16 gap-4">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('home') }}" class="text-2xl font-bold" style="color: var(--color-primary);">
                                {{ $siteName }}
                            </a>
                        </div>
                        
                        <!-- Search Bar (Centered) -->
                        <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                            <form action="{{ route('products.index') }}" method="GET" class="w-full">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        value="{{ request('search') }}"
                                        placeholder="পণ্য খুঁজুন..." 
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                                        style="transition: all var(--transition-fast);"
                                    >
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1 rounded-md font-bangla" style="background-color: var(--color-primary); color: white;">
                                        খুঁজুন
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Right Menu -->
                        <div class="flex items-center gap-4">
                            <a href="{{ route('products.index') }}" class="hidden lg:inline-block text-gray-700 hover:text-primary transition font-bangla">পণ্য</a>
                                
                            @if(auth()->check())
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                          
                            </a>
                        @elseif(auth()->user()->isSponsor())
                            <a href="{{ route('sponsor.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                              
                            </a>
                        @endif
                                <!-- User Avatar with Dropdown -->
                                <div class="relative" x-data="{ userMenuOpen: false }">
                                    <button 
                                        @click="userMenuOpen = !userMenuOpen"
                                        class="flex items-center focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-full transition"
                                    >
                                        @if(auth()->user()->photo)
                                            <img src="{{ Storage::disk('public')->url(auth()->user()->photo) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 hover:border-primary transition-colors">
                                       
                                            @else
                                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center border-2 border-gray-200 hover:border-primary transition-colors">
                                                <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </button>
                                    <!-- Dropdown Menu -->
                                    <div 
                                        x-show="userMenuOpen"
                                        @click.away="userMenuOpen = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                                        style="display: none;"
                                    >
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->affiliate_code }}</p>
                                        </div>
                                       
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                <span class="font-bangla">লগআউট</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary transition font-bangla">লগইন</a>
                            @endif
                            
                            <!-- Mobile menu button -->
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
              
            </nav>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-transition class="md:hidden bg-white border-t border-gray-200 shadow-lg">
                <div class="px-4 py-4 space-y-3">
                    <!-- Mobile Search -->
                    <form action="{{ route('products.index') }}" method="GET" class="mb-4">
                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="পণ্য খুঁজুন..." 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                            >
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                    
                    <a href="{{ route('home') }}" class="block text-gray-700 hover:text-primary font-bangla">হোম</a>
                    <a href="{{ route('products.index') }}" class="block text-gray-700 hover:text-primary font-bangla">পণ্য</a>
                    @if($categories->count() > 0)
                        @foreach($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="block text-gray-700 hover:text-primary font-bangla pl-4">{{ $category->name }}</a>
                        @endforeach
                    @endif
                  
                </div>
            </div>
        </header>
        @endunless
        
        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>
        
        @unless(isset($hideLayoutChrome) && $hideLayoutChrome)
        <!-- Footer -->
        <footer class="bg-gray-50 border-t border-gray-200 mt-8 md:mt-16 pattern-dots">
            @php
                $footerSettings = json_decode(\App\Models\Setting::get('footer_settings', '{}'), true) ?? [];
                $siteName = \App\Models\Setting::get('site_name', config('app.name'));
                $contactPhone = \App\Models\Setting::get('contact_phone', '');
                $contactEmail = \App\Models\Setting::get('contact_email', '');
                $year = date('Y');
                $defaultColumns = [
                    [
                        'title' => 'আমাদের সম্পর্কে',
                        'type' => 'text',
                        'content' => 'আমাদের অনলাইন শপে আপনাকে স্বাগতম। আমরা গুণগত মানসম্পন্ন পণ্য সরবরাহ করি।'
                    ],
                    [
                        'title' => 'দ্রুত লিংক',
                        'type' => 'links',
                        'links' => [
                            ['text' => 'হোম', 'url' => route('home')],
                            ['text' => 'সব পণ্য', 'url' => route('products.index')],
                        ]
                    ],
                    [
                        'title' => 'গ্রাহক সেবা',
                        'type' => 'service',
                        'items' => [
                            ['icon' => '📞', 'text' => $contactPhone],
                            ['icon' => '✉️', 'text' => $contactEmail],
                            ['icon' => '🚚', 'text' => 'ফ্রি ডেলিভারি'],
                            ['icon' => '💳', 'text' => 'ক্যাশ অন ডেলিভারি'],
                        ]
                    ],
                    [
                        'title' => 'আমাদের প্রতিশ্রুতি',
                        'type' => 'badges',
                        'items' => [
                            ['icon' => '✅', 'text' => '৩০ দিনের মানি-ব্যাক গ্যারান্টি'],
                            ['icon' => '✅', 'text' => '১০০% অরিজিনাল পণ্য'],
                            ['icon' => '✅', 'text' => 'নিরাপদ পেমেন্ট'],
                            ['icon' => '✅', 'text' => '২৪/৭ গ্রাহক সেবা'],
                        ]
                    ],
                ];
                $columns = $footerSettings['columns'] ?? $defaultColumns;
                while (count($columns) < 4) { $columns[] = ['title' => '', 'type' => 'text', 'content' => '']; }
                $columns = array_slice($columns, 0, 4);
                $copyright = $footerSettings['copyright'] ?? "&copy; {{ year }} {{ site_name }}। সর্বস্বত্ব সংরক্ষিত।";
                $renderText = function($text) use ($siteName, $contactPhone, $contactEmail, $year) {
                    return str_replace(
                        [
                            '{{ site_name }}', '@{{ site_name }}', '{{site_name}}', '@{{site_name}}',
                            '{{ contact_phone }}', '@{{ contact_phone }}', '{{contact_phone}}', '@{{contact_phone}}',
                            '{{ contact_email }}', '@{{ contact_email }}', '{{contact_email}}', '@{{contact_email}}',
                            '{{ year }}', '@{{ year }}', '{{year}}', '@{{year}}',
                        ],
                        [
                            $siteName, $siteName, $siteName, $siteName,
                            $contactPhone, $contactPhone, $contactPhone, $contactPhone,
                            $contactEmail, $contactEmail, $contactEmail, $contactEmail,
                            $year, $year, $year, $year,
                        ],
                        $text ?? ''
                    );
                };
            @endphp
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                    @foreach($columns as $column)
                        <div>
                            @if(!empty($column['title']))
                                <h3 class="text-sm md:text-lg font-semibold mb-2 md:mb-4 text-gray-900">{{ $renderText($column['title']) }}</h3>
                            @endif
                            
                            @if(($column['type'] ?? 'text') === 'text')
                                @if(!empty($column['content']))
                                    <p class="text-gray-600 text-xs md:text-sm leading-relaxed">{{ $renderText($column['content']) }}</p>
                                @endif
                            @elseif(($column['type'] ?? '') === 'links')
                                @php $links = $column['links'] ?? []; @endphp
                                @if(!empty($links))
                                    <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
                                        @foreach($links as $link)
                                            @if(!empty($link['text']) || !empty($link['url']))
                                                <li>
                                                    <a href="{{ $link['url'] ?? '#' }}" class="text-gray-600 hover:text-primary transition">{{ $renderText($link['text'] ?? '') }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            @elseif(in_array(($column['type'] ?? ''), ['service', 'badges']))
                                @php $items = $column['items'] ?? []; @endphp
                                @if(!empty($items))
                                    <ul class="space-y-1 md:space-y-2 text-xs md:text-sm text-gray-600">
                                        @foreach($items as $item)
                                            @if(!empty($item['text']) || !empty($item['icon']))
                                                <li>{{ $item['icon'] ?? '' }} {{ $renderText($item['text'] ?? '') }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- App Download Button -->
                <div class="mt-4 md:mt-8 flex gap-5 flex-col items-center justify-center">
                    <span class="font-bangla text-sm md:text-lg font-semibold">অ্যাপ ডাউনলোড করুন</span>
                    
                    <a 
                        href="https://drive.google.com/file/d/1VlemahBqWaE07Pjy2oHk0kIC1HT1d9Mb/view?usp=sharing" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2  bg-[#ffffff] hover:bg-[#ffffff]/80 text-white rounded-lg transition shadow-md hover:shadow-lg font-sans font-medium"
                        aria-label="Download app from Google Drive"
                    >
                        <img src="{{ asset('google-play-download.png') }}" alt="Google Play" class="h-12 md:h-[80px] w-auto" />
                       
                    </a>
                </div>
                
                <div class="mt-4 md:mt-8 pt-4 md:pt-8 border-t border-gray-200 text-center">
                    <p class="text-xs md:text-sm text-gray-600">
                        {!! $renderText($copyright) !!}
                    </p>
                </div>
            </div>
        </footer>
        @endunless
    </div>
    
    <!-- jQuery (required for Slick) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Slick JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    
    @stack('scripts')
    
    <!-- WhatsApp Floating Button -->
    <a 
        href="https://wa.me/8801789079791" 
        target="_blank" 
        rel="noopener noreferrer"
        class="fixed bottom-8 left-4 md:left-8 z-50 flex items-center justify-center w-14 h-14 md:w-16 md:h-16 bg-[#25D366] text-white rounded-full shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-200"
        aria-label="Chat on WhatsApp"
    >
        <i class="fab fa-whatsapp text-2xl md:text-3xl"></i>
    </a>
    
    <!-- Scroll to Top Button -->
    <button 
        id="scrollToTopBtn" 
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"
        class="fixed bottom-8 right-4 md:right-8 bg-primary text-white rounded-full p-3 md:p-4 shadow-lg hover:shadow-xl transition-all duration-200 opacity-0 pointer-events-none z-50"
        style="background-color: var(--color-primary);"
        aria-label="Scroll to top"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
    
    <script>
        // Show/hide scroll to top button
        window.addEventListener('scroll', function() {
            const scrollBtn = document.getElementById('scrollToTopBtn');
            if (window.pageYOffset > 300) {
                scrollBtn.classList.remove('opacity-0', 'pointer-events-none');
                scrollBtn.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                scrollBtn.classList.remove('opacity-100', 'pointer-events-auto');
                scrollBtn.classList.add('opacity-0', 'pointer-events-none');
            }
        });
    </script>
    
    <style>
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        
        /* Marquee Animation */
        .marquee-container {
            overflow: hidden;
            width: 100%;
        }
        .marquee-content {
            display: inline-block;
            animation: marquee-scroll 30s linear infinite;
            padding-right: 50px;
        }
        @keyframes marquee-scroll {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        .marquee-content:hover {
            animation-play-state: paused;
        }
    </style>
</body>
</html>

