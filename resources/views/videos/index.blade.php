@extends('layouts.app')

@section('title', 'ভিডিও')
@section('description', 'আমাদের ভিডিও কালেকশন দেখুন')

@section('content')
<div class="bg-white py-12" x-data="{ videoLightbox: { open: false, embedUrl: '', title: '' } }" x-effect="videoLightbox.open ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-bangla mb-8 text-center">ভিডিও</h1>

        <!-- Category Filter -->
        @if($categories->isNotEmpty())
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <a href="{{ route('videos.index') }}" 
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                সব
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('videos.index', ['category' => $cat]) }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') == $cat ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
        @endif

        <!-- Videos Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
            @forelse($videos as $video)
            <button type="button" @click="videoLightbox = { open: true, embedUrl: '{{ $video->embed_url }}', title: '{{ addslashes($video->title) }}' }" class="group block w-full text-left cursor-pointer">
                <div class="relative aspect-video overflow-hidden rounded-lg bg-gray-200">
                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/40 transition">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center shadow-lg border border-white/80 bg-transparent">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $video->category }}</p>
                <h3 class="font-semibold text-gray-900 line-clamp-2 text-sm font-sans">{{ $video->title }}</h3>
            </button>
            @empty
            <div class="col-span-full text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 font-bangla">কোনো ভিডিও পাওয়া যাচ্ছে না।</p>
            </div>
            @endforelse
        </div>

        @if($videos->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $videos->links() }}
        </div>
        @endif
    </div>

    <!-- Video Lightbox - x-if removes iframe from DOM when closed, stopping video/audio -->
    <template x-if="videoLightbox.open">
        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="videoLightbox.open = false"
             @click.self="videoLightbox.open = false"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/90">
            <div @click.stop class="relative w-full max-w-4xl">
                <button @click="videoLightbox.open = false" class="absolute -top-12 right-0 text-white hover:text-gray-300 p-2" aria-label="Close">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="aspect-video rounded-lg overflow-hidden bg-black">
                    <iframe :src="videoLightbox.embedUrl + '?autoplay=1'" class="w-full h-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <p x-text="videoLightbox.title" class="mt-3 text-white text-center font-sans"></p>
            </div>
        </div>
    </template>
</div>
@endsection
