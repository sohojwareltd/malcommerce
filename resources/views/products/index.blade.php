@extends('layouts.app')

@section('title', request('category') ? $categories->firstWhere('id', request('category'))->name ?? 'পণ্য' : 'সব পণ্য')
@section('description', request('category') ? 'ক্যাটাগরি থেকে পণ্য ব্রাউজ করুন' : 'আমাদের সব পণ্য ব্রাউজ করুন')

@section('content')
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-2">
                @if(request('category'))
                    {{ $categories->firstWhere('id', request('category'))->name ?? 'পণ্য' }}
                @else
                    সব পণ্য
                @endif
            </h1>
            @if(request('search'))
            <p class="text-gray-600 font-bangla">
                "{{ request('search') }}" এর জন্য ফলাফল
            </p>
            @endif
        </div>

        @php
            $queryForType = function ($type) {
                return route('products.index', array_merge(request()->except(['type', 'page']), ['type' => $type]));
            };
        @endphp
        
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="card sticky top-24">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 font-bangla">ক্যাটাগরি</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('products.index', request()->only(['type'])) }}" 
                               class="block px-4 py-2 rounded-lg font-bangla transition {{ !request('category') ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }}"
                               style="{{ !request('category') ? 'background-color: var(--color-primary);' : '' }}">
                                সব পণ্য
                            </a>
                        </li>
                        @foreach($categories as $category)
                        <li>
                            <a href="{{ route('products.index', array_merge(request()->only(['type']), ['category' => $category->id])) }}" 
                               class="block px-4 py-2 rounded-lg font-bangla transition {{ request('category') == $category->id ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }}"
                               style="{{ request('category') == $category->id ? 'background-color: var(--color-primary);' : '' }}">
                                {{ $category->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
            
            <!-- Products -->
            <main class="flex-1">
                <!-- Type tabs: All / Physical / Digital -->
                <nav class="flex flex-wrap gap-2 mb-6 font-bangla" aria-label="পণ্যের ধরন">
                    <a href="{{ $queryForType('all') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $type === 'all' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" style="{{ $type === 'all' ? 'background-color: var(--color-primary);' : '' }}">সব পণ্য</a>
                    <a href="{{ $queryForType('physical') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $type === 'physical' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" style="{{ $type === 'physical' ? 'background-color: var(--color-primary);' : '' }}">ফিজিক্যাল পণ্য</a>
                    <a href="{{ $queryForType('digital') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $type === 'digital' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" style="{{ $type === 'digital' ? 'background-color: var(--color-primary);' : '' }}">ডিজিটাল পণ্য</a>
                </nav>

                <!-- Search Bar -->
                <form method="GET" action="{{ route('products.index') }}" class="mb-8">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('type') && request('type') !== 'all')
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="পণ্য খুঁজুন..." 
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                            style="transition: all var(--transition-fast);"
                        >
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 btn-primary font-bangla px-6 py-2 rounded-lg">
                            খুঁজুন
                        </button>
                    </div>
                </form>

                @if($type === 'all')
                    {{-- Two sections: Physical and Digital --}}
                    @php
                        $physicalCount = $physicalProducts instanceof \Illuminate\Pagination\AbstractPaginator ? $physicalProducts->total() : $physicalProducts->count();
                        $digitalCount = $digitalProducts instanceof \Illuminate\Pagination\AbstractPaginator ? $digitalProducts->total() : $digitalProducts->count();
                    @endphp

                    @if($physicalCount > 0 || $digitalCount > 0)
                        @if($physicalCount > 0)
                        <section class="mb-12">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-gray-900 font-bangla">ফিজিক্যাল পণ্য</h2>
                                <a href="{{ $queryForType('physical') }}" class="text-sm font-medium" style="color: var(--color-primary);">সব দেখুন →</a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @foreach($physicalProducts as $product)
                                    @include('products.partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </section>
                        @endif

                        @if($digitalCount > 0)
                        <section>
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-gray-900 font-bangla">ডিজিটাল পণ্য</h2>
                                <a href="{{ $queryForType('digital') }}" class="text-sm font-medium" style="color: var(--color-primary);">সব দেখুন →</a>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @foreach($digitalProducts as $product)
                                    @include('products.partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </section>
                        @endif
                    @else
                        <div class="col-span-full text-center py-16">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-xl font-semibold text-gray-900 font-bangla mb-2">কোনো পণ্য পাওয়া যায়নি</h3>
                            <p class="text-gray-600 font-bangla mb-6">আপনার সার্চ বা ক্যাটাগরি পরিবর্তন করুন</p>
                            <a href="{{ route('products.index') }}" class="btn-primary font-bangla inline-block">সব পণ্য দেখুন</a>
                        </div>
                    @endif
                @else
                    {{-- Single type: physical or digital with pagination --}}
                    @php
                        $products = $type === 'physical' ? $physicalProducts : $digitalProducts;
                        $sectionTitle = $type === 'physical' ? 'ফিজিক্যাল পণ্য' : 'ডিজিটাল পণ্য';
                    @endphp

                    @if($products->count() > 0)
                    <div class="mb-6 text-gray-600 font-bangla">
                        মোট {{ $products->total() }}টি পণ্য পাওয়া গেছে
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            @include('products.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    @if($products->hasPages())
                    <div class="mt-12">
                        {{ $products->links() }}
                    </div>
                    @endif
                    @else
                    <div class="col-span-full text-center py-16">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold text-gray-900 font-bangla mb-2">কোনো পণ্য পাওয়া যায়নি</h3>
                        <p class="text-gray-600 font-bangla mb-6">আপনার সার্চ বা ক্যাটাগরি পরিবর্তন করুন</p>
                        <a href="{{ route('products.index') }}" class="btn-primary font-bangla inline-block">সব পণ্য দেখুন</a>
                    </div>
                    @endif
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
