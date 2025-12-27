@extends('layouts.builder')

@section('title', 'Page Builder')
@section('subtitle', $product->name)

@section('header-actions')
    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-sm text-[#637381] hover:text-[#202223] px-3 py-1.5 rounded hover:bg-[#F6F6F7] transition">
        View Page
    </a>
    <a href="{{ route('admin.products.edit', $product) }}" class="text-sm text-[#637381] hover:text-[#202223] px-3 py-1.5 rounded hover:bg-[#F6F6F7] transition">
        Back to Edit
    </a>
    <form action="{{ route('admin.products.update', $product) }}" method="POST" id="save-layout-form" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="page_layout" id="page-layout-input">
        <input type="hidden" name="name" value="{{ $product->name }}">
        <input type="hidden" name="slug" value="{{ $product->slug }}">
        <input type="hidden" name="category_id" value="{{ $product->category_id }}">
        <input type="hidden" name="description" value="{{ $product->description }}">
        <input type="hidden" name="short_description" value="{{ $product->short_description }}">
        <input type="hidden" name="price" value="{{ $product->price }}">
        <input type="hidden" name="compare_at_price" value="{{ $product->compare_at_price }}">
        <input type="hidden" name="sku" value="{{ $product->sku }}">
        <input type="hidden" name="stock_quantity" value="{{ $product->stock_quantity }}">
        <input type="hidden" name="is_active" value="{{ $product->is_active ? 1 : 0 }}">
        <input type="hidden" name="is_featured" value="{{ $product->is_featured ? 1 : 0 }}">
        <input type="hidden" name="sort_order" value="{{ $product->sort_order }}">
        @if($product->images && is_array($product->images))
            @foreach($product->images as $image)
                <input type="hidden" name="images[]" value="{{ $image }}">
            @endforeach
        @endif
    </form>
    <button 
        type="button" 
        onclick="saveLayout()" 
        class="bg-[#008060] text-white px-4 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] transition"
    >
        Save Layout
    </button>
@endsection

@section('content')
@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 mx-6 mt-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

<div id="page-builder-editor" 
     class="h-full"
     data-sections='@json($product->page_layout ?? [])'
     data-product-id="{{ $product->id }}"
     data-product-price="{{ $product->price }}"
     data-product-compare-price="{{ $product->compare_at_price ?? '' }}"
     data-product-in-stock="{{ $product->in_stock ? '1' : '0' }}"
     data-product-stock-quantity="{{ $product->stock_quantity }}"></div>

@push('scripts')
@vite('resources/js/page-builder.js')

<script>
    function saveLayout() {
        const sections = window.currentSections || [];
        const form = document.getElementById('save-layout-form');
        const input = document.getElementById('page-layout-input');
        
        if (input) {
            input.value = JSON.stringify(sections);
            form.submit();
        }
    }
</script>
@endpush
@endsection

