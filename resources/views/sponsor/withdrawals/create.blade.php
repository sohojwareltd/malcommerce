@extends('layouts.sponsor')

@section('title', 'Request Withdrawal')

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
                <h1 class="text-base sm:text-lg md:text-xl font-bold" style="color: var(--color-dark);">Request Withdrawal</h1>
                <p class="text-xs sm:text-sm mt-1" style="color: var(--color-medium);">Available balance: ৳{{ number_format($user->balance ?? 0, 2) }}</p>
            </div>
            <a href="{{ route('sponsor.withdrawals.index') }}" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold whitespace-nowrap" style="background: var(--color-accent); color: var(--color-dark);">
                ← Back
            </a>
        </div>
    </div>

    @if(empty($methods) || !$defaultMethod)
    <div class="app-card mx-4 mb-4 p-4">
        <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded-lg mb-4">
            <p class="text-xs sm:text-sm text-red-700">You need to add at least one default MFS withdrawal method before requesting a withdrawal.</p>
        </div>
        <a href="{{ route('sponsor.withdrawal-methods') }}" class="px-4 py-2 rounded-lg text-white text-xs sm:text-sm font-semibold inline-block" style="background: var(--color-medium);">
            Manage Methods
        </a>
    </div>
    @else
    <div class="app-card mx-4 mb-4 p-3 sm:p-4 md:p-6 max-w-md">
        <form action="{{ route('sponsor.withdrawals.store') }}" method="POST" class="space-y-3 sm:space-y-4">
            @csrf
            <div>
                <label class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Withdrawal Method</label>
                <select name="method_key" class="w-full border-2 rounded-xl px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm focus:outline-none" style="border-color: var(--color-accent);">
                    @foreach($methods as $key => $method)
                        @php
                            $number = $method['number'] ?? '';
                            $masked = $number ? substr($number, 0, 3) . 'XXX-XXXXX' : '';
                        @endphp
                        <option value="{{ $key }}" {{ $defaultMethod && $defaultMethod === $method ? 'selected' : '' }}>
                            {{ strtoupper($method['provider']) }} - {{ $masked }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] sm:text-xs mt-1" style="color: var(--color-medium);">Withdrawals are sent only to verified MFS numbers.</p>
                <a href="{{ route('sponsor.withdrawal-methods') }}" class="text-[10px] sm:text-xs underline" style="color: var(--color-medium);">Manage methods</a>
                @error('method_key')<p class="text-xs sm:text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Amount (৳)</label>
                <input type="number" name="amount" step="0.01" min="1" value="{{ old('amount') }}" class="w-full border-2 rounded-xl px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm focus:outline-none" style="border-color: var(--color-accent);">
                @error('amount')<p class="text-xs sm:text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-4 py-2 rounded-lg text-white text-xs sm:text-sm font-semibold" style="background: var(--color-medium);">
                    Submit Withdrawal Request
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection


