@extends('layouts.sponsor')

@section('title', 'Withdrawals')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Withdrawals</h1>
        <p class="text-neutral-600 text-sm mt-1">Current balance: ৳{{ number_format($user->balance ?? 0, 2) }}</p>
    </div>
    <a href="{{ route('sponsor.withdrawals.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">Withdraw income</a>
</div>

<table class="min-w-full bg-white border border-neutral-200 text-sm">
    <thead>
        <tr class="bg-neutral-100">
            <th class="px-3 py-2 text-left border-b">Requested At</th>
            <th class="px-3 py-2 text-left border-b">Amount</th>
            <th class="px-3 py-2 text-left border-b">Status</th>
            <th class="px-3 py-2 text-left border-b">Method</th>
            <th class="px-3 py-2 text-left border-b">Processed At</th>
        </tr>
    </thead>
    <tbody>
        @forelse($withdrawals as $withdrawal)
        <tr>
            <td class="px-3 py-2 border-b">{{ $withdrawal->requested_at?->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2 border-b font-semibold">৳{{ number_format($withdrawal->amount, 2) }}</td>
            <td class="px-3 py-2 border-b">{{ ucfirst($withdrawal->status) }}</td>
            <td class="px-3 py-2 border-b">
                {{ $withdrawal->receiving_account_information['label'] ?? ($withdrawal->receiving_account_information['type'] ?? '-') }}
            </td>
            <td class="px-3 py-2 border-b">
                {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('Y-m-d H:i') : '-' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-3 py-4 text-center text-neutral-500">No withdrawals yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $withdrawals->links() }}
</div>
@endsection



