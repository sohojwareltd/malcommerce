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
    <!-- Header -->
    

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 px-4">

      <!-- Sidebar -->
      <div class="space-y-4">
            <!-- User Info -->
            <div class="app-card p-3 sm:p-4">
                <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4" style="color: var(--color-dark);">User Information</h2>
                
                <div class="flex justify-center mb-4">
                    @if($referral->photo)
                        <button type="button" class="focus:outline-none" aria-label="Open photo">
                            <img
                                src="{{ Storage::disk('public')->url($referral->photo) }}"
                                alt="{{ $referral->name }}"
                                data-lightbox-src="{{ Storage::disk('public')->url($referral->photo) }}"
                                class="w-40 h-40 sm:w-24 sm:h-24 rounded-full object-cover border-[3px] border-white shadow-lg cursor-zoom-in"
                            >
                        </button>
                    @else
                        <div class="w-40 h-40 sm:w-24 sm:h-24 rounded-full flex items-center justify-center border-[3px] border-white shadow-lg" style="background: var(--color-light);">
                            <svg class="w-16 h-16 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

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
                
                <dl class="space-y-3">
                    <div>
                        <dt class="text-[10px] sm:text-xs font-medium" style="color: var(--color-medium);">Name</dt>
                        <dd class="mt-1 text-xs sm:text-sm font-semibold" style="color: var(--color-dark);">{{ $referral->name }}</dd>
                    </div>
                    
                    @if($referral->phone)
                    <div>
                        <dt class="text-[10px] sm:text-xs font-medium" style="color: var(--color-medium);">Phone</dt>
                        <dd class="mt-1 text-xs sm:text-sm" style="color: var(--color-dark);">{{ $referral->phone }}</dd>
                    </div>
                    @endif
                
                    <div>
                        <dt class="text-[10px] sm:text-xs font-medium" style="color: var(--color-medium);">Affiliate Code</dt>
                        <dd class="mt-1 text-xs sm:text-sm font-mono font-semibold" style="color: var(--color-dark);">{{ $referral->affiliate_code }}</dd>
                    </div>
                    
                    @if($referral->address)
                    <div>
                        <dt class="text-[10px] sm:text-xs font-medium" style="color: var(--color-medium);">Address</dt>
                        <dd class="mt-1 text-xs sm:text-sm whitespace-pre-line" style="color: var(--color-dark);">{{ $referral->address }}</dd>
                    </div>
                    @endif
                    
                    <div>
                        <dt class="text-[10px] sm:text-xs font-medium" style="color: var(--color-medium);">Joined</dt>
                        <dd class="mt-1 text-xs sm:text-sm" style="color: var(--color-dark);">{{ $referral->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Actions -->
            <div class="app-card p-3 sm:p-4">
                <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4" style="color: var(--color-dark);">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('sponsor.users.edit', $referral) }}" 
                       class="block w-full text-center px-4 py-2 rounded-lg text-white text-xs sm:text-sm font-semibold" style="background: var(--color-medium);">
                        Edit User
                    </a>
                    <a href="{{ route('sponsor.dashboard') }}" 
                       class="block w-full text-center px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold" style="background: var(--color-accent); color: var(--color-dark);">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Statistics -->
            <div class="grid grid-cols-2 gap-3">
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

            <!-- Orders -->
            <div class="app-card p-3 sm:p-4">
                <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4" style="color: var(--color-dark);">Orders ({{ $referral->customerOrders->count() }})</h2>
                @if($referral->customerOrders->count() > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($referral->customerOrders->take(20) as $order)
                    <div class="p-3 rounded-xl border-2" style="border-color: var(--color-accent); background: var(--color-accent);">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
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
                                <p class="text-xs sm:text-sm font-medium" style="color: var(--color-dark);">{{ $order->product->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs sm:text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</p>
                                <p class="text-[10px] sm:text-xs mt-1" style="color: var(--color-medium);">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto mb-3" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-sm" style="color: var(--color-medium);">No orders yet</p>
                </div>
                @endif
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
