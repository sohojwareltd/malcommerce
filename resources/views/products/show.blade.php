@extends('layouts.app')

@php
    // Set product-specific meta tags for SEO and social sharing
    $productDescription = $product->short_description ?? $product->description ?? 'পণ্যের বিবরণ দেখুন';
    $productImage = $product->main_image;
    
    // Convert image URL to absolute URL for Facebook sharing (Facebook requires absolute URLs)
    if ($productImage) {
        // Check if it's already a full URL (http:// or https://)
        if (!filter_var($productImage, FILTER_VALIDATE_URL)) {
            // If it starts with /storage/, it's already a public path
            if (strpos($productImage, '/storage/') === 0) {
                $productImage = url($productImage);
            }
            // If it starts with /, use url() helper to make it absolute
            elseif (strpos($productImage, '/') === 0) {
                $productImage = url($productImage);
            } 
            // If it contains 'storage/', assume it's a storage path
            elseif (strpos($productImage, 'storage/') !== false) {
                $productImage = url('/' . $productImage);
            }
            // Otherwise, assume it's a storage path and prepend /storage/
            else {
                $productImage = url('/storage/' . $productImage);
            }
        }
    } else {
        // Fallback to default image
        $productImage = asset('favicon.ico');
    }
    
    // Override meta tags for this product page
    $metaDescOverride = $productDescription;
    $ogImageOverride = $productImage;
    $ogTypeOverride = 'product';
@endphp

@section('title', $product->name)
@section('description', $productDescription)

@section('content')
@if($product->page_layout && is_array($product->page_layout) && count($product->page_layout) > 0)
    <!-- Page Builder Content (Full Page) -->
    <div id="custom-sections" 
         data-layout="{{ json_encode($product->page_layout) }}"
         data-product-id="{{ $product->id }}"
         data-product-name="{{ $product->name }}"
         data-product-image="{{ $product->main_image ?? '' }}"
         data-product-short-description="{{ $product->short_description ?? '' }}"
         data-product-price="{{ $product->price }}"
         data-product-compare-price="{{ $product->compare_at_price ?? '' }}"
         data-product-in-stock="{{ $product->in_stock ? '1' : '0' }}"
         data-product-stock-quantity="{{ $product->stock_quantity }}">
        <!-- Loading indicator -->
        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 mx-auto mb-4" style="border-color: var(--color-primary);"></div>
                <p class="text-gray-600 font-bangla">পেজ লোড হচ্ছে...</p>
            </div>
        </div>
    </div>
@else
    <!-- Fallback: Standard Product Page -->
    <div class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Images -->
                <div>
                    <div class="card overflow-hidden p-0 mb-4">
                        <img 
                            src="{{ $product->main_image ?? '/placeholder-product.jpg' }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-auto object-cover"
                            id="main-product-image"
                        >
                    </div>
                    @if($product->images && count($product->images) > 1)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($product->images as $image)
                        <button 
                            onclick="document.getElementById('main-product-image').src = '{{ $image }}'"
                            class="border-2 border-gray-200 hover:border-primary rounded-lg overflow-hidden transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <img 
                                src="{{ $image }}" 
                                alt="{{ $product->name }}" 
                                class="w-full h-24 object-cover"
                            >
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <!-- Product Info -->
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 font-bangla">{{ $product->name }}</h1>
                    
                    <!-- Price -->
                    <div class="mb-6">
                        <div class="flex items-center gap-4 mb-2">
                            <span class="text-3xl md:text-4xl font-bold" style="color: var(--color-primary);">৳{{ number_format($product->price, 0) }}</span>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            <span class="text-xl text-gray-500 line-through">৳{{ number_format($product->compare_at_price, 0) }}</span>
                            <span class="badge-sale font-bangla">
                                {{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}% ছাড়
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="mb-6">
                        @if($product->in_stock)
                        <span class="badge-new font-bangla">
                            ✅ স্টকে আছে
                        </span>
                        @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700 font-bangla">
                            ❌ স্টকে নেই
                        </span>
                        @endif
                    </div>
                    
                    @if($product->short_description)
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                        <p class="text-gray-700 font-bangla leading-relaxed">{{ $product->short_description }}</p>
                    </div>
                    @endif
                    
                    <!-- Order Form -->
                    @if($product->in_stock)
                    <div id="order-form-section">
                        @include('products.partials.order-form', ['product' => $product])
                    </div>
                    @else
                    <div class="card text-center py-8">
                        <p class="text-gray-600 font-bangla text-lg">এই পণ্যটি বর্তমানে স্টকে নেই</p>
                    </div>
                    @endif
                    
                    <!-- Description Tabs -->
                    @if($product->description)
                    <div class="mt-12" x-data="{ activeTab: 'description' }">
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="flex gap-6">
                                <button 
                                    @click="activeTab = 'description'" 
                                    :class="activeTab === 'description' ? 'border-b-2 font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                                    class="pb-3 font-bangla transition-colors"
                                    style="border-bottom-color: activeTab === 'description' ? 'var(--color-primary)' : 'transparent';"
                                >
                                    বিবরণ
                                </button>
                                <button 
                                    @click="activeTab = 'shipping'" 
                                    :class="activeTab === 'shipping' ? 'border-b-2 font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                                    class="pb-3 font-bangla transition-colors"
                                    style="border-bottom-color: activeTab === 'shipping' ? 'var(--color-primary)' : 'transparent';"
                                >
                                    ডেলিভারি তথ্য
                                </button>
                            </nav>
                        </div>
                        
                        <div x-show="activeTab === 'description'" x-transition class="prose max-w-none">
                            <div class="text-gray-700 font-bangla leading-relaxed whitespace-pre-line">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'shipping'" x-transition class="space-y-4 font-bangla">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-2">🚚 ডেলিভারি</h3>
                                <p class="text-gray-700">সারা বাংলাদেশে ফ্রি হোম ডেলিভারি।</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-2">💳 পেমেন্ট</h3>
                                <p class="text-gray-700">ক্যাশ অন ডেলিভারি (COD) - ডেলিভারির সময় পেমেন্ট করুন।</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-2">⏰ ডেলিভারি সময়</h3>
                                <p class="text-gray-700">৩-৫ কার্যদিবস (অর্ডার নিশ্চিত হওয়ার পর থেকে)।</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="bg-gray-50 py-16 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-8 text-gray-900 font-bangla">সম্পর্কিত পণ্য</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <div class="card card-hover group">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="block">
                        <div class="relative overflow-hidden rounded-lg mb-4 bg-gray-100" style="aspect-ratio: 1/1;">
                            <img 
                                src="{{ $relatedProduct->main_image ?? '/placeholder-product.jpg' }}" 
                                alt="{{ $relatedProduct->name }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        </div>
                        <h3 class="font-semibold text-lg mb-2 text-gray-900 font-bangla line-clamp-2">{{ $relatedProduct->name }}</h3>
                        <div class="text-xl font-bold" style="color: var(--color-primary);">৳{{ number_format($relatedProduct->price, 0) }}</div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
@endif

@push('scripts')
@if($product->page_layout && count($product->page_layout) > 0)
@vite('resources/js/product-sections.js')
@endif
@endpush

@push('head')
<!-- Product Structured Data (JSON-LD) for SEO -->
@php
    $structuredData = [
        "@context" => "https://schema.org",
        "@type" => "Product",
        "name" => $product->name,
        "description" => $productDescription,
        "image" => $productImage,
        "sku" => $product->sku ?? '',
        "offers" => [
            "@type" => "Offer",
            "url" => url()->current(),
            "priceCurrency" => "BDT",
            "price" => (string) $product->price,
            "priceValidUntil" => now()->addYear()->toDateString(),
            "availability" => "https://schema.org/" . ($product->in_stock ? 'InStock' : 'OutOfStock'),
            "itemCondition" => "https://schema.org/NewCondition"
        ],
        "brand" => [
            "@type" => "Brand",
            "name" => \App\Models\Setting::get('site_name', config('app.name'))
        ]
    ];
    
    if ($product->category) {
        $structuredData["category"] = $product->category->name;
    }
@endphp
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endsection
