@extends('layouts.builder')

@section('title', 'Page Builder - ' . $product->name)

@section('subtitle', 'Build custom sections for: ' . $product->name)

@section('header-actions')
<a href="{{ route('admin.products.edit', $product) }}" class="text-[#637381] hover:text-[#202223] px-3 py-1.5 text-sm font-medium hover:bg-[#F6F6F7] rounded transition">
    Back
</a>
<a href="{{ route('products.show', $product->slug) }}" target="_blank" class="bg-[#008060] text-white px-4 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] transition shadow-sm">
    Preview
</a>
@endsection

@section('content')
<!-- Theme Builder Container - Full Width -->
<div id="theme-builder-container" 
     data-sections="{{ json_encode($product->page_layout ?? []) }}" 
     data-product-id="{{ $product->id }}"
     data-product-slug="{{ $product->slug }}"
     data-product-name="{{ $product->name }}"
     data-product-description="{{ $product->description ?? '' }}"
     data-product-short-description="{{ $product->short_description ?? '' }}"
     data-product-price="{{ $product->price }}"
     data-product-compare-price="{{ $product->compare_at_price ?? '' }}"
     data-product-images="{{ json_encode($product->images ?? []) }}"
     class="h-full">
    <div class="flex items-center justify-center h-full">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
            <p class="text-neutral-600">Loading Page Builder...</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Full height builder */
    #theme-builder-container {
        height: 100%;
        width: 100%;
    }
    
    /* Prevent body scroll */
    body {
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
@vite('resources/js/builder.js')
@endpush
@endsection

