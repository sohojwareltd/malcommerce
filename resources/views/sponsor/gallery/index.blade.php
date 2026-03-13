@extends('layouts.sponsor')

@section('title', 'Gallery')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@push('styles')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
@endpush

@section('content')
<div class="space-y-6">
    <div id="upload" class="bg-white rounded-2xl shadow-sm border border-neutral-200 p-4 sm:p-6">
        <h2 class="text-lg font-bold text-neutral-900 mb-4">Upload Photo</h2>
        <form action="{{ route('sponsor.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-neutral-700 mb-1">Upload for</label>
                    <select name="user_id" id="user_id" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                        <option value="{{ $sponsor->id }}" {{ (old('user_id', $targetUserId ?? $sponsor->id) == $sponsor->id) ? 'selected' : '' }}>
                            Myself ({{ $sponsor->name }})
                        </option>
                        @foreach($referrals as $referral)
                            <option value="{{ $referral->id }}" {{ (old('user_id', $targetUserId ?? null) == $referral->id) ? 'selected' : '' }}>
                                {{ $referral->name }} @if($referral->phone) - {{ $referral->phone }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="caption" class="block text-sm font-medium text-neutral-700 mb-1">Caption (optional)</label>
                    <input type="text" name="caption" id="caption" value="{{ old('caption') }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary" placeholder="Short description">
                    @error('caption')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <label for="photo" class="block text-sm font-medium text-neutral-700 mb-1">Photo</label>
                <input
                    type="file"
                    name="photo"
                    id="photo"
                    accept="image/*"
                    capture="environment"
                    class="block w-full text-sm text-neutral-700"
                >
                <p class="mt-1 text-xs text-neutral-500">
                    Tap to take a photo or choose from your gallery. JPEG, PNG, GIF, or WebP.
                </p>
                @error('photo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="pt-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary hover:bg-primary/90">
                    Upload
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h2 class="text-lg font-bold text-neutral-900">Gallery Photos</h2>
            <form method="GET" action="{{ route('sponsor.gallery.index') }}" class="flex items-center gap-2">
                <label for="filter_user_id" class="text-xs text-neutral-600">Filter by user:</label>
                <select name="user_id" id="filter_user_id" class="rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                    <option value="">All</option>
                    <option value="{{ $sponsor->id }}" {{ request('user_id') == $sponsor->id ? 'selected' : '' }}>
                        Myself ({{ $sponsor->name }})
                    </option>
                    @foreach($referrals as $referral)
                        <option value="{{ $referral->id }}" {{ request('user_id') == $referral->id ? 'selected' : '' }}>
                            {{ $referral->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-primary hover:bg-primary/90">
                    Apply
                </button>
            </form>
        </div>

        @if($photos->count() === 0)
            <p class="text-sm text-neutral-500">No photos uploaded yet.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($photos as $photo)
                    <div class="group relative bg-neutral-50 rounded-xl overflow-hidden border border-neutral-200">
                        <button type="button" class="w-full h-full" aria-label="Open photo"
                                data-lightbox-src="{{ Storage::disk('public')->url($photo->path) }}">
                            <div class="aspect-square overflow-hidden bg-neutral-100">
                                <img
                                    src="{{ Storage::disk('public')->url($photo->path) }}"
                                    alt="{{ $photo->caption ?? 'Photo' }}"
                                    class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105"
                                >
                            </div>
                        </button>
                        <div class="p-2">
                            @if($photo->caption)
                                <p class="text-xs text-neutral-800 truncate">{{ $photo->caption }}</p>
                            @endif
                            <p class="mt-1 text-[11px] text-neutral-500">
                                For: {{ $photo->user->id === $sponsor->id ? 'Myself' : $photo->user->name }}
                            </p>
                            <p class="text-[11px] text-neutral-400">
                                By: {{ $photo->uploader->id === $sponsor->id ? 'Me' : $photo->uploader->name }}
                            </p>
                        </div>
                        <form
                            action="{{ route('sponsor.gallery.destroy', $photo) }}"
                            method="POST"
                            class="absolute top-1 right-1"
                            onsubmit="return confirm('Delete this photo?');"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-600/90 text-white text-xs shadow-sm hover:bg-red-700">
                                ×
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $photos->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Lightbox -->
<div id="gallery-lightbox" class="fixed inset-0 z-[9999] hidden">
    <div id="gallery-lightbox-backdrop" class="absolute inset-0 bg-black/80"></div>
    <div class="relative h-full w-full flex items-center justify-center p-4">
        <button type="button" id="gallery-lightbox-close" class="absolute top-4 right-4 rounded-full px-3 py-2 text-white/90 hover:text-white" aria-label="Close">
            ✕
        </button>
        <img id="gallery-lightbox-img" src="" alt="Photo" class="max-h-[85vh] max-w-[95vw] rounded-2xl shadow-2xl object-contain">
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.hash === '#upload') {
                const uploadSection = document.getElementById('upload');
                if (uploadSection) {
                    uploadSection.scrollIntoView({ behavior: 'smooth' });
                }
            }

            const inputElement = document.querySelector('input[id="photo"]');
            if (inputElement && window.FilePond) {
                FilePond.registerPlugin(
                    window.FilePondPluginImagePreview,
                    window.FilePondPluginFileValidateType,
                );

                FilePond.create(inputElement, {
                    allowMultiple: false,
                    storeAsFile: true,
                    credits: false,
                    acceptedFileTypes: ['image/*'],
                    labelIdle: 'Tap to take a photo or <span class="filepond--label-action">browse</span>',
                });
            }

            // Simple lightbox for gallery images
            const lightbox = document.getElementById('gallery-lightbox');
            const lightboxImg = document.getElementById('gallery-lightbox-img');
            const backdrop = document.getElementById('gallery-lightbox-backdrop');
            const closeBtn = document.getElementById('gallery-lightbox-close');
            const triggers = document.querySelectorAll('[data-lightbox-src]');

            if (lightbox && lightboxImg && backdrop && closeBtn && triggers.length) {
                const open = (src) => {
                    lightboxImg.src = src;
                    lightbox.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                };

                const close = () => {
                    lightbox.classList.add('hidden');
                    lightboxImg.src = '';
                    document.body.classList.remove('overflow-hidden');
                };

                triggers.forEach((btn) => {
                    btn.addEventListener('click', () => open(btn.getAttribute('data-lightbox-src')));
                });

                backdrop.addEventListener('click', close);
                closeBtn.addEventListener('click', close);
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
                        close();
                    }
                });
            }
        });
    </script>
@endpush

