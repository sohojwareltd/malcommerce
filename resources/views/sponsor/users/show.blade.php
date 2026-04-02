@extends('layouts.sponsor')

@section('title', 'Affiliate User Details')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
    :root {
        --color-dark: #0F2854;
        --color-medium: #1C4D8D;
        --color-light: #4988C4;
        --color-accent: #BDE8F5;
    }
    
    .app-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 40, 84, 0.08);
    }
    
    .stat-card {
        background: linear-gradient(135deg, var(--color-medium) 0%, var(--color-light) 100%);
        border-radius: 16px;
        padding: 16px;
        color: white;
        position: relative;
        overflow: hidden;
    }
</style>

<div class="min-h-screen pb-6" style="">
    <div class="px-4">
        <!-- Lightbox -->
        <div id="photo-lightbox" class="fixed inset-0 z-[9999] hidden">
            <div id="photo-lightbox-backdrop" class="absolute inset-0 bg-black/80"></div>
            <div class="relative h-full w-full flex items-center justify-center p-4">
                <button type="button" id="photo-lightbox-close" class="absolute top-4 right-4 rounded-full px-3 py-2 text-white/90 hover:text-white" aria-label="Close">
                    ✕
                </button>
                <img id="photo-lightbox-img" src="" alt="Photo" class="max-h-[85vh] max-w-[95vw] rounded-2xl shadow-2xl object-contain">
            </div>
        </div>

        <div class="app-card p-3 sm:p-4" x-data="{ tab: 'information' }">
            <!-- Compact actions -->
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if($referral->photo)
                        <button type="button" class="flex items-center gap-3 min-w-0 focus:outline-none" aria-label="Open photo">
                            <img
                                src="{{ Storage::disk('public')->url($referral->photo) }}"
                                alt="{{ $referral->name }}"
                                data-lightbox-src="{{ Storage::disk('public')->url($referral->photo) }}"
                                class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 shadow-sm cursor-zoom-in"
                                style="border-color: var(--color-accent);"
                            >
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate" style="color: var(--color-dark);">{{ $referral->name }}</p>
                                <p class="text-[11px] font-mono truncate" style="color: var(--color-medium);">{{ $referral->affiliate_code }}</p>
                            </div>
                        </button>
                    @else
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full flex items-center justify-center border-2 shadow-sm" style="background: var(--color-light); border-color: var(--color-accent);">
                                <span class="text-white font-bold text-sm">{{ substr($referral->name, 0, 1) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate" style="color: var(--color-dark);">{{ $referral->name }}</p>
                                <p class="text-[11px] font-mono truncate" style="color: var(--color-medium);">{{ $referral->affiliate_code }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if($referral->phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $referral->phone) }}"
                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition hover:opacity-90"
                           style="border-color: rgba(28,77,141,0.25); color: var(--color-medium); background: white;">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1H9c-1.1 0-2-.9-2-2v-3.5c0-.55.45-1 1-1h1.5c0-1.25.2-2.45.57-3.57.11-.35.03-.74-.25-1.02l-2.2-2.2z"/></svg>
                            <span class="hidden sm:inline">Call</span>
                            <span class="sm:hidden">Call</span>
                        </a>
                    @endif

                    <a href="{{ route('sponsor.users.edit', $referral) }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition hover:opacity-90"
                       style="border-color: rgba(28,77,141,0.25); color: var(--color-medium); background: white;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Edit
                    </a>

                    <a href="{{ route('sponsor.gallery.index', ['user_id' => $referral->id]) }}#upload"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition hover:opacity-90"
                       style="border-color: rgba(28,77,141,0.25); color: var(--color-medium); background: white;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5-5 5 5"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v11"></path>
                        </svg>
                        Upload
                    </a>

                    <a href="{{ route('sponsor.dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition hover:opacity-90"
                       style="border-color: rgba(28,77,141,0.25); color: var(--color-medium); background: white;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-2 border-b mb-4 pb-2" style="border-color: rgba(189, 232, 245, 0.9);">
                <button type="button"
                        @click="tab = 'information'"
                        class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition"
                        :class="tab === 'information' ? 'text-white' : 'text-[rgba(15,40,84,0.8)] hover:bg-neutral-100'"
                        :style="tab === 'information' ? 'background: var(--color-medium);' : ''">
                    Information
                </button>
                <button type="button"
                        @click="tab = 'referrals'"
                        class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition"
                        :class="tab === 'referrals' ? 'text-white' : 'text-[rgba(15,40,84,0.8)] hover:bg-neutral-100'"
                        :style="tab === 'referrals' ? 'background: var(--color-medium);' : ''">
                    Referrals
                    <span class="ml-1 text-[10px] sm:text-xs opacity-90">({{ $referral->referrals->count() }})</span>
                </button>
                <button type="button"
                        @click="tab = 'orders'"
                        class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition"
                        :class="tab === 'orders' ? 'text-white' : 'text-[rgba(15,40,84,0.8)] hover:bg-neutral-100'"
                        :style="tab === 'orders' ? 'background: var(--color-medium);' : ''">
                    Orders
                    <span class="ml-1 text-[10px] sm:text-xs opacity-90">({{ $referral->customerOrders->count() }})</span>
                </button>
            </div>

            <!-- Information tab -->
            <div x-show="tab === 'information'">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-1">
                        <h2 class="text-base sm:text-lg font-bold mb-3" style="color: var(--color-dark);">User Information</h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs sm:text-sm font-medium" style="color: var(--color-medium);">Name</dt>
                                <dd class="mt-1 text-sm sm:text-base font-semibold" style="color: var(--color-dark);">{{ $referral->name }}</dd>
                            </div>
                            @if($referral->phone)
                            <div>
                                <dt class="text-xs sm:text-sm font-medium" style="color: var(--color-medium);">Phone</dt>
                                <dd class="mt-1 text-sm sm:text-base" style="color: var(--color-dark);">{{ $referral->phone }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-xs sm:text-sm font-medium" style="color: var(--color-medium);">Affiliate Code</dt>
                                <dd class="mt-1 text-sm sm:text-base font-mono font-semibold" style="color: var(--color-dark);">{{ $referral->affiliate_code }}</dd>
                            </div>
                            @if($referral->address)
                            <div>
                                <dt class="text-xs sm:text-sm font-medium" style="color: var(--color-medium);">Address</dt>
                                <dd class="mt-1 text-sm sm:text-base whitespace-pre-line" style="color: var(--color-dark);">{{ $referral->address }}</dd>
                            </div>
                            @endif
                            @if($referral->comment)
                            <div>
                                <dt class="text-xs sm:text-sm font-medium" style="color: var(--color-medium);">Comment</dt>
                                <dd class="mt-1 text-sm sm:text-base whitespace-pre-line" style="color: var(--color-dark);">{{ $referral->comment }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-xs sm:text-sm font-medium" style="color: var(--color-medium);">Joined</dt>
                                <dd class="mt-1 text-sm sm:text-base" style="color: var(--color-dark);">{{ $referral->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        @if($referral->galleryPhotos && $referral->galleryPhotos->count() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-base sm:text-lg font-bold" style="color: var(--color-dark);">Recent Photos</h2>
                                <a href="{{ route('sponsor.gallery.index', ['user_id' => $referral->id]) }}"
                                   class="text-xs sm:text-sm font-semibold"
                                   style="color: var(--color-medium);">
                                    View in Gallery →
                                </a>
                            </div>
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                                @foreach($referral->galleryPhotos->take(12) as $photo)
                                    <div class="relative rounded-xl overflow-hidden border border-[rgba(189,232,245,0.8)] bg-white">
                                        <div class="aspect-square overflow-hidden">
                                            <img
                                                src="{{ Storage::disk('public')->url($photo->path) }}"
                                                alt="{{ $photo->caption ?? 'Photo' }}"
                                                class="w-full h-full object-cover"
                                            >
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Referrals tab -->
            <div x-show="tab === 'referrals'" style="display: none;">
                @if($referral->referrals->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($referral->referrals as $child)
                            <a href="{{ route('sponsor.users.show', $child) }}"
                               class="block p-3 rounded-xl border hover:shadow-sm transition"
                               style="border-color: rgba(189,232,245,0.9); background: rgba(189,232,245,0.25);">
                                <div class="flex items-center gap-3">
                                    @if($child->photo)
                                        <img src="{{ Storage::disk('public')->url($child->photo) }}" alt="{{ $child->name }}" class="w-10 h-10 rounded-full object-cover border-2" style="border-color: white;">
                                    @else
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2" style="background: var(--color-light); border-color: white;">
                                            <span class="text-white font-bold text-sm">{{ substr($child->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold truncate" style="color: var(--color-dark);">{{ $child->name }}</p>
                                        <p class="text-[11px] font-mono truncate" style="color: var(--color-medium);">{{ $child->affiliate_code }}</p>
                                        @if($child->phone)
                                            <p class="text-[11px] truncate" style="color: rgba(15,40,84,0.75);">{{ $child->phone }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[11px] font-semibold" style="color: var(--color-dark);">{{ $child->customer_orders_count ?? 0 }}</p>
                                        <p class="text-[10px]" style="color: rgba(15,40,84,0.6);">orders</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <p class="text-sm" style="color: var(--color-medium);">No referrals found for this user.</p>
                    </div>
                @endif
            </div>

            <!-- Orders tab -->
            <div x-show="tab === 'orders'" style="display: none;">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="stat-card text-center">
                        <p class="text-[10px] sm:text-xs text-white/80 mb-1">Total Orders</p>
                        <p class="text-xl sm:text-2xl font-bold text-white">{{ $stats['total_orders'] }}</p>
                    </div>
                    <div class="stat-card text-center" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                        <p class="text-[10px] sm:text-xs text-white/80 mb-1">Total Revenue</p>
                        <p class="text-xl sm:text-2xl font-bold text-white">৳{{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="stat-card text-center" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                        <p class="text-[10px] sm:text-xs text-white/80 mb-1">Pending</p>
                        <p class="text-xl sm:text-2xl font-bold text-white">{{ $stats['pending_orders'] }}</p>
                    </div>
                    <div class="stat-card text-center" style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);">
                        <p class="text-[10px] sm:text-xs text-white/80 mb-1">Delivered</p>
                        <p class="text-xl sm:text-2xl font-bold text-white">{{ $stats['delivered_orders'] }}</p>
                    </div>
                </div>

                <div>
                    <h2 class="text-base sm:text-lg font-bold mb-3" style="color: var(--color-dark);">Orders</h2>
                    @if($referral->customerOrders->count() > 0)
                        <div class="space-y-2 max-h-[32rem] overflow-y-auto">
                            @foreach($referral->customerOrders->take(30) as $order)
                                <div class="p-3 rounded-xl border-2" style="border-color: var(--color-accent); background: var(--color-accent);">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-mono font-semibold" style="color: var(--color-dark);">#{{ $order->order_number }}</span>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                       ($order->status === 'delivered' ? 'bg-green-100 text-green-800' :
                                                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                                       'bg-blue-100 text-blue-800')) }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs sm:text-sm font-medium truncate" style="color: var(--color-dark);">{{ $order->product->name }}</p>
                                        </div>
                                        <div class="text-right pl-3">
                                            <p class="text-xs sm:text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</p>
                                            <p class="text-[10px] sm:text-xs mt-1" style="color: var(--color-medium);">{{ $order->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <p class="text-sm" style="color: var(--color-medium);">No orders yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const triggerImg = document.querySelector('img[data-lightbox-src]');
    const lightbox = document.getElementById('photo-lightbox');
    const lightboxImg = document.getElementById('photo-lightbox-img');
    const backdrop = document.getElementById('photo-lightbox-backdrop');
    const closeBtn = document.getElementById('photo-lightbox-close');

    if (!triggerImg || !lightbox || !lightboxImg || !backdrop || !closeBtn) return;

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

    triggerImg.addEventListener('click', () => open(triggerImg.getAttribute('data-lightbox-src')));
    backdrop.addEventListener('click', close);
    closeBtn.addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) close();
    });
});
</script>
@endpush
