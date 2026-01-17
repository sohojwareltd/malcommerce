@extends('layouts.sponsor')

@section('title', 'Withdrawals')

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

<div class="min-h-screen pb-6">
    <!-- Header -->
    <div class="app-card mx-4 mt-4 mb-4 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-base sm:text-lg md:text-xl font-bold" style="color: var(--color-dark);">Withdrawals</h1>
                <p class="text-xs sm:text-sm mt-1" style="color: var(--color-medium);">Current balance: ৳{{ number_format($user->balance ?? 0, 2) }}</p>
            </div>
            <a href="{{ route('sponsor.withdrawals.create') }}" class="px-4 py-2 rounded-lg text-white text-xs sm:text-sm font-semibold whitespace-nowrap" style="background: var(--color-medium);">
                Withdraw income
            </a>
        </div>
    </div>

    <!-- Withdrawals List -->
    <div class="app-card mx-4 mb-4 overflow-hidden">
        @if($withdrawals->count() > 0)
        <div class="divide-y" style="border-color: var(--color-accent);">
            @foreach($withdrawals as $withdrawal)
            <div class="p-3 sm:p-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-xs sm:text-sm font-semibold" style="color: var(--color-dark);">৳{{ number_format($withdrawal->amount, 2) }}</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                {{ $withdrawal->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($withdrawal->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($withdrawal->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                   'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        </div>
                        <p class="text-[10px] sm:text-xs mb-1" style="color: var(--color-medium);">
                            Method: {{ $withdrawal->receiving_account_information['label'] ?? ($withdrawal->receiving_account_information['type'] ?? '-') }}
                        </p>
                        <p class="text-[10px] sm:text-xs" style="color: var(--color-medium);">
                            Requested: {{ $withdrawal->requested_at?->format('M d, Y h:i A') }}
                        </p>
                        @if($withdrawal->processed_at)
                        <p class="text-[10px] sm:text-xs mt-1" style="color: var(--color-medium);">
                            Processed: {{ $withdrawal->processed_at->format('M d, Y h:i A') }}
                        </p>
                        @endif
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
            <p class="text-sm font-medium mb-2" style="color: var(--color-medium);">No withdrawals yet</p>
            <p class="text-xs" style="color: var(--color-light);">Your withdrawal requests will appear here</p>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($withdrawals->hasPages())
    <div class="mx-4 mt-4">
        {{ $withdrawals->links() }}
    </div>
    @endif
</div>
@endsection



