@extends('layouts.sponsor')

@section('title', 'Request Withdrawal')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Request Withdrawal</h1>
    <p class="text-neutral-600 text-sm mt-1">Available balance: ৳{{ number_format($user->balance ?? 0, 2) }}</p>
</div>

@if(empty($methods) || !$defaultMethod)
    <p class="text-sm text-red-600 mb-4">You need to add at least one default MFS withdrawal method before requesting a withdrawal.</p>
    <a href="{{ route('sponsor.withdrawal-methods') }}" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">Manage Methods</a>
@else
    <form action="{{ route('sponsor.withdrawals.store') }}" method="POST" class="space-y-4 max-w-md bg-white p-4 border border-neutral-200 rounded-lg">
        @csrf
        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-1">Withdrawal Method</label>
            <select name="method_key" class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm">
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
            <p class="text-[11px] text-neutral-500 mt-1">Withdrawals are sent only to verified MFS numbers.</p>
            <a href="{{ route('sponsor.withdrawal-methods') }}" class="text-[11px] text-primary underline">Manage methods</a>
            @error('method_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-1">Amount (৳)</label>
            <input type="number" name="amount" step="0.01" min="1" value="{{ old('amount') }}" class="w-full border border-neutral-300 rounded-lg px-3 py-2 text-sm">
            @error('amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">Submit Withdrawal Request</button>
    </form>
@endif
@endsection


