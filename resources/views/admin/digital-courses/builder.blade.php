@extends('layouts.builder')

@section('title', 'Course Page Builder')
@section('subtitle', $course->title)

@section('header-actions')
    <a href="{{ route('courses.show', $course) }}" target="_blank" class="text-sm text-[#637381] hover:text-[#202223] px-3 py-1.5 rounded hover:bg-[#F6F6F7] transition">
        View Page
    </a>
    <a href="{{ route('admin.digital-courses.edit', $course) }}" class="text-sm text-[#637381] hover:text-[#202223] px-3 py-1.5 rounded hover:bg-[#F6F6F7] transition">
        Back to Edit
    </a>
    <form action="{{ route('admin.digital-courses.update', $course) }}" method="POST" id="save-layout-form" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="page_layout" id="page-layout-input">
        <input type="hidden" name="title" value="{{ $course->title }}">
        <input type="hidden" name="slug" value="{{ $course->slug }}">
        <input type="hidden" name="category_id" value="{{ $course->category_id }}">
        <input type="hidden" name="short_description" value="{{ $course->short_description }}">
        <input type="hidden" name="description" value="{{ $course->description }}">
        <input type="hidden" name="price" value="{{ $course->price }}">
        <input type="hidden" name="compare_at_price" value="{{ $course->compare_at_price }}">
        <input type="hidden" name="preview_youtube_url" value="{{ $course->preview_youtube_url }}">
        <input type="hidden" name="sort_order" value="{{ $course->sort_order }}">
        <input type="hidden" name="is_active" value="{{ $course->is_active ? 1 : 0 }}">
        <input type="hidden" name="is_featured" value="{{ $course->is_featured ? 1 : 0 }}">
        <input type="hidden" name="checkout_form_title" value="{{ $course->checkout_form_title }}">
        <input type="hidden" name="checkout_button_text" value="{{ $course->checkout_button_text }}">
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

<script type="application/json" id="page-builder-initial-sections">@json($course->page_layout ?? [])</script>
@php
    $builderCheckoutSettings = [
        'title' => $course->checkout_form_title ?: 'কোর্স কিনুন',
        'buttonText' => $course->checkout_button_text ?: 'bKash দিয়ে কিনুন',
    ];
    $courseImage = $course->thumbnail_url;
@endphp
<div id="page-builder-editor"
     class="h-full"
     data-entity-type="course"
     data-product-id="{{ $course->id }}"
     data-product-name="{{ $course->title }}"
     data-product-image="{{ $courseImage }}"
     data-product-price="{{ $course->price }}"
     data-product-compare-price="{{ $course->compare_at_price ?? '' }}"
     data-product-in-stock="1"
     data-product-stock-quantity="999"
     data-course-slug="{{ $course->slug }}"
     data-checkout-settings="{{ json_encode($builderCheckoutSettings) }}"></div>

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
