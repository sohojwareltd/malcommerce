@extends('layouts.sponsor')

@section('title', 'Earnings')

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
</style>

<div class="min-h-screen pb-6" style="background: linear-gradient(to bottom, var(--color-accent) 0%, #f0f9ff 100%);">
    <!-- Header -->
    <div class="app-card mx-4 mt-4 mb-4 p-4">
        <div>
            <h1 class="text-base sm:text-lg md:text-xl font-bold mb-2" style="color: var(--color-dark);">Earnings</h1>
            <div class="flex flex-wrap gap-4 text-xs sm:text-sm">
                <div>
                    <span style="color: var(--color-medium);">Current Balance:</span>
                    <span class="font-bold ml-1" style="color: var(--color-dark);">৳{{ number_format($user->balance ?? 0, 2) }}</span>
                </div>
                <div>
                    <span style="color: var(--color-medium);">Total Earnings:</span>
                    <span class="font-bold ml-1 text-green-600">৳{{ number_format($stats['total_earnings'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Table -->
    <div class="app-card mx-4 mb-4 overflow-hidden">
        @if($earnings->count() > 0)
        <div class="divide-y" style="border-color: var(--color-accent);">
            @foreach($earnings as $earning)
            <div class="p-3 sm:p-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-xs sm:text-sm font-semibold" style="color: var(--color-dark);">{{ ucfirst($earning->earning_type) }}</span>
                            <span class="text-xs font-mono" style="color: var(--color-medium);">{{ $earning->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($earning->order)
                        <p class="text-[10px] sm:text-xs mb-1" style="color: var(--color-medium);">Order: #{{ $earning->order->order_number }}</p>
                        @endif
                        @if($earning->referral)
                        <p class="text-[10px] sm:text-xs mb-1" style="color: var(--color-medium);">Referral: {{ $earning->referral->name }}</p>
                        @endif
                        @if($earning->comment)
                        <p class="text-[10px] sm:text-xs mt-1" style="color: var(--color-medium);">{{ $earning->comment }}</p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm sm:text-base font-bold text-green-600">+৳{{ number_format($earning->amount, 2) }}</p>
                        <p class="text-[10px] sm:text-xs mt-1" style="color: var(--color-medium);">{{ $earning->created_at->format('h:i A') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 sm:p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm font-medium mb-2" style="color: var(--color-medium);">No earnings yet</p>
            <p class="text-xs" style="color: var(--color-light);">Earnings will appear here when you receive commissions</p>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($earnings->hasPages())
    <div class="mx-4 mt-4">
        {{ $earnings->links() }}
    </div>
    @endif
</div>
@endsection
