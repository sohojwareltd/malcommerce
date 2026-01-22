@extends('layouts.app')

@section('title', 'হোম')
@section('description')
    {{ \App\Models\Setting::get('meta_description', 'আমাদের অনলাইন শপ থেকে সেরা পণ্য কিনুন। ফ্রি ডেলিভারি ও ক্যাশ অন ডেলিভারি সুবিধা।') }}
@endsection

@section('content')
    <!-- Hero Section with Slick Slider -->
    @php
        $heroSlides = json_decode(\App\Models\Setting::get('hero_slider', '[]'), true);
        // Fallback to default slide if no slides are configured
        if (empty($heroSlides)) {
            $heroSlides = [[
                'title' => \App\Models\Setting::get('hero_title', 'আমাদের অনলাইন শপে আপনাকে স্বাগতম'),
                'subtitle' => \App\Models\Setting::get('hero_subtitle', 'গুণগত মানসম্পন্ন পণ্য আপনার দোরগোড়ায়'),
                'cta' => 'পণ্য দেখুন',
                'link' => route('products.index'),
                'image' => null
            ]];
        }
    @endphp
    @if(!empty($heroSlides))
        <div class="relative bg-white pattern-dots overflow-hidden">
            <div class="w-full lg:w-[70%] lg:mx-auto">
                <div class="hero-slider">
                @foreach($heroSlides as $slide)
                    @php
                        $hasImage = !empty($slide['image']);
                        $placement = $slide['placement'] ?? 'center-center';
                        
                        // Placement classes mapping for flexbox
                        $placementClasses = [
                            'center-center' => ['flex' => 'items-center justify-center', 'text' => 'center', 'width' => 'max-w-3xl mx-auto'],
                            'center-left' => ['flex' => 'items-center justify-start', 'text' => 'left', 'width' => 'max-w-3xl'],
                            'center-right' => ['flex' => 'items-center justify-end', 'text' => 'right', 'width' => 'max-w-3xl ml-auto'],
                            'top-center' => ['flex' => 'items-start justify-center', 'text' => 'center', 'width' => 'max-w-3xl mx-auto'],
                            'top-left' => ['flex' => 'items-start justify-start', 'text' => 'left', 'width' => 'max-w-3xl'],
                            'top-right' => ['flex' => 'items-start justify-end', 'text' => 'right', 'width' => 'max-w-3xl ml-auto'],
                            'bottom-center' => ['flex' => 'items-end justify-center', 'text' => 'center', 'width' => 'max-w-3xl mx-auto'],
                            'bottom-left' => ['flex' => 'items-end justify-start', 'text' => 'left', 'width' => 'max-w-3xl'],
                            'bottom-right' => ['flex' => 'items-end justify-end', 'text' => 'right', 'width' => 'max-w-3xl ml-auto'],
                        ];
                        $placementConfig = $placementClasses[$placement] ?? $placementClasses['center-center'];
                        $placementFlexClass = $placementConfig['flex'];
                        $placementTextAlign = $placementConfig['text'];
                        $placementWidthClass = $placementConfig['width'];
                        
                        // Colors with defaults
                        $titleColor = $slide['title_color'] ?? ($hasImage ? '#FFFFFF' : '#000000');
                        $subtitleColor = $slide['subtitle_color'] ?? ($hasImage ? '#FFFFFF' : '#666666');
                        $buttonBgColor = $slide['button_bg_color'] ?? 'var(--color-primary)';
                        $buttonTextColor = $slide['button_text_color'] ?? '#FFFFFF';
                    @endphp
                    <div class="hero-slide">
                        @if($hasImage)
                            <div class="hero-slide-with-image">
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] ?? 'Hero Slide' }}" class="hero-slide-image">
                               
                                <div class="hero-slide-content-wrapper absolute inset-0 flex {{ $placementFlexClass }}">
                                    <div class="w-full px-4 sm:px-6 lg:px-8 py-8 md:py-16 max-w-full sm:max-w-[540px] md:max-w-[720px] lg:max-w-[960px] xl:max-w-[1140px] 2xl:max-w-[1320px] mx-auto">
                                        <div class="{{ $placementWidthClass }}">
                                            <div class="hero-slide-content" style="text-align: {{ $placementTextAlign }};">
                                                @if(!empty($slide['title']))
                                                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 font-bangla" style="color: {{ $titleColor }};">
                                                        {{ $slide['title'] }}
                                                    </h1>
                                                @endif
                                                @if(!empty($slide['subtitle']))
                                                    <p class="text-xl md:text-2xl mb-8 font-bangla" style="color: {{ $subtitleColor }};">
                                                        {{ $slide['subtitle'] }}
                                                    </p>
                                                @endif
                                                @if(!empty($slide['cta']) && !empty($slide['link']))
                                                    <a href="{{ $slide['link'] }}"
                                                        class="inline-block font-bangla text-lg px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200"
                                                        style="background-color: {{ $buttonBgColor }}; color: {{ $buttonTextColor }};">
                                                        <span>{{ $slide['cta'] }}</span>
                                                        <i class="fas fa-arrow-right ml-2"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="hero-slide-no-image bg-white">
                                <div class="w-full px-4 sm:px-6 lg:px-8 py-8 md:py-12 max-w-full sm:max-w-[540px] md:max-w-[720px] lg:max-w-[960px] xl:max-w-[1140px] 2xl:max-w-[1320px] mx-auto">
                                    <div class="hero-slide-content text-center max-w-3xl mx-auto">
                                        @if(!empty($slide['title']))
                                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 font-bangla" style="color: {{ $titleColor }};">
                                                {{ $slide['title'] }}
                                            </h1>
                                        @endif
                                        @if(!empty($slide['subtitle']))
                                            <p class="text-xl md:text-2xl mb-8 font-bangla" style="color: {{ $subtitleColor }};">
                                                {{ $slide['subtitle'] }}
                                            </p>
                                        @endif
                                        @if(!empty($slide['cta']) && !empty($slide['link']))
                                            <a href="{{ $slide['link'] }}"
                                                class="inline-block font-bangla text-lg px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200"
                                                style="background-color: {{ $buttonBgColor }}; color: {{ $buttonTextColor }};">
                                                <span>{{ $slide['cta'] }}</span>
                                                <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    @endif
    
    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.hero-slider').slick({
                autoplay: true,
                autoplaySpeed: 5000,
                fade: true,
                cssEase: 'linear',
                speed: 600,
                dots: true,
                arrows: true,
                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
                infinite: true,
                pauseOnHover: false,
                pauseOnFocus: false,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            arrows: false
                        }
                    }
                ]
            });
        });
    </script>
    <style>
        .hero-slider {
            width: 100%;
            position: relative;
        }
        
        .hero-slide {
            outline: none;
            position: relative;
        }
        
        .hero-slide-with-image {
            position: relative;
            width: 100%;
        }
        
        .hero-slide-image {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
        }
        
        .hero-slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }
        
        .hero-slide-content-wrapper {
            z-index: 2;
        }
        
        .hero-slide-content {
            position: relative;
            z-index: 3;
        }
        
        
        .hero-slide-no-image {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-slide-no-image .hero-slide-content {
            text-align: center;
        }
        
        /* Navigation Arrows */
        .hero-slider .slick-prev,
        .hero-slider .slick-next {
            z-index: 10;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            color: var(--color-primary);
            border: none;
            cursor: pointer;
        }
        
        .hero-slider .slick-prev:hover,
        .hero-slider .slick-next:hover {
            background: white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .hero-slider .slick-prev {
            left: 30px;
        }
        
        .hero-slider .slick-next {
            right: 30px;
        }
        
        .hero-slider .slick-prev i,
        .hero-slider .slick-next i {
            font-size: 20px;
            font-weight: bold;
        }
        
        /* Dots */
        .hero-slider .slick-dots {
            bottom: 30px;
            z-index: 10;
        }
        
        .hero-slider .slick-dots li button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--color-primary);
            opacity: 0.5;
            border: none;
            cursor: pointer;
        }
        
        .hero-slider .slick-dots li button:before {
            display: none;
        }
        
        .hero-slider .slick-dots li.slick-active button {
            opacity: 1;
            background: var(--color-primary);
        }
        
        /* Override default Slick styles */
        .hero-slider .slick-dots li {
            width: 12px;
            height: 12px;
            margin: 0 6px;
        }
    </style>
    @endpush

    <!-- Trust Section -->
    @php
        $homeFeatures = json_decode(\App\Models\Setting::get('home_features', '[]'), true);
        if (empty($homeFeatures)) {
            // Default features if not set
            $homeFeatures = [
                ['icon' => 'fas fa-truck', 'title' => 'ফ্রি ডেলিভারি', 'description' => 'সারা দেশে'],
                ['icon' => 'fas fa-money-bill-wave', 'title' => 'ক্যাশ অন ডেলিভারি', 'description' => 'নিরাপদ পেমেন্ট'],
                ['icon' => 'fas fa-shield-alt', 'title' => '৩০ দিন গ্যারান্টি', 'description' => 'মানি-ব্যাক'],
                ['icon' => 'fas fa-headset', 'title' => '২৪/৭ সাপোর্ট', 'description' => 'সাহায্য পাওয়া যাবে'],
            ];
        }
        // Filter out empty features
        $homeFeatures = array_filter($homeFeatures, function($feature) {
            return !empty($feature['title']) || !empty($feature['icon']);
        });
    @endphp
    
    @if(!empty($homeFeatures))
    <div class="bg-gray-50 border-y border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                @foreach($homeFeatures as $feature)
                    @if(!empty($feature['title']) || !empty($feature['icon']))
                    <div class="flex flex-col items-center bg-[#f0f0f0] border border-gray-200 p-4 rounded-lg">
                        @if(!empty($feature['icon']))
                        <div class="text-3xl sm:text-4xl mb-1.5 sm:mb-2" style="color: var(--color-primary);">
                            <i class="{{ $feature['icon'] }}"></i>
                        </div>
                        @endif
                        @if(!empty($feature['title']))
                        <h3 class="font-semibold text-gray-900 font-bangla mb-0.5 text-xs sm:text-sm">{{ $feature['title'] }}</h3>
                        @endif
                        @if(!empty($feature['description']))
                        <p class="text-xs sm:text-sm text-gray-600 font-bangla leading-snug">{{ $feature['description'] }}</p>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Featured Products Section -->
    @if ($featuredProducts->count() > 0)
        <div class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-center text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2">বিশেষ অফার</h2>
                    <p class="text-gray-600 font-bangla mb-3">এখনই কিনুন, বিশেষ মূল্যে</p>
                    <a href="{{ route('products.index') }}"
                        class="mt-2 text-primary hover:underline font-bangla font-medium inline-block">
                        সব অফার দেখুন <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($featuredProducts->take(8) as $product)
                        <div class="card card-hover group">
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                <div class="relative overflow-hidden rounded-lg mb-4 bg-gray-100"
                                    style="aspect-ratio: 1/1;">
                                    <img src="{{ $product->main_image ?? '/placeholder-product.jpg' }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                                        <span class="absolute top-3 left-3 badge-sale font-bangla">
                                            {{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}%
                                            ছাড়
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-lg mb-2 text-gray-900 font-bangla line-clamp-2">
                                    {{ $product->name }}</h3>
                                <div class="flex items-center gap-3">
                                    <span class="text-xl font-bold"
                                        style="color: var(--color-primary);">৳{{ number_format($product->price, 0) }}</span>
                                    @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                                        <span
                                            class="text-sm text-gray-500 line-through">৳{{ number_format($product->compare_at_price, 0) }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-8 md:hidden">
                    <a href="{{ route('products.index') }}" class="inline-block btn-primary font-bangla px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-white font-medium">
                        সব অফার দেখুন
                    </a>
                </div>
            </div>
        </div>
    @endif



    <!-- Latest Products Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class=" mb-10">
                <div class="text-center">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2 ">Top Seller পণ্য</h2>
                    <a href="{{ route('products.index') }}"
                    class="hidden md:inline-block text-primary hover:underline font-bangla font-medium">
                    সব পণ্য দেখুন 
                </a>
                </div>
                
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                    <div class="card card-hover group">
                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                            <div class="relative overflow-hidden rounded-lg mb-4 bg-gray-100" style="aspect-ratio: 1/1;">
                                <img src="{{ $product->main_image ?? '/placeholder-product.jpg' }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <span class="absolute top-3 left-3 badge-sale font-bangla">
                                        {{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}%
                                        ছাড়
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-lg mb-2 text-gray-900 font-bangla line-clamp-2">
                                {{ $product->name }}</h3>
                            <div class="flex items-center gap-3">
                                <span class="text-xl font-bold"
                                    style="color: var(--color-primary);">৳{{ number_format($product->price, 0) }}</span>
                                @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <span
                                        class="text-sm text-gray-500 line-through">৳{{ number_format($product->compare_at_price, 0) }}</span>
                                @endif
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg font-bangla">এখনই কোনো পণ্য পাওয়া যাচ্ছে না।</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-8 md:hidden">
                <a href="{{ route('products.index') }}" class="inline-block btn-primary font-bangla px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-white font-medium">
                    সব পণ্য দেখুন
                </a>
            </div>
        </div>
    </div>

 
    </div>
@endsection
