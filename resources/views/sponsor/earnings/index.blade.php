@extends('layouts.sponsor')

@section('title', 'Earnings')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Earnings</h1>
    <p class="text-neutral-600 text-sm mt-1">Current balance: ৳{{ number_format($user->balance ?? 0, 2) }} | Total earnings: ৳{{ number_format($stats['total_earnings'] ?? 0, 2) }}</p>
</div>

<table class="min-w-full bg-white border border-neutral-200 text-sm">
    <thead>
        <tr class="bg-neutral-100">
            <th class="px-3 py-2 text-left border-b">Date</th>
            <th class="px-3 py-2 text-left border-b">Type</th>
            <th class="px-3 py-2 text-left border-b">Amount</th>
            <th class="px-3 py-2 text-left border-b">Order</th>
            <th class="px-3 py-2 text-left border-b">Referral</th>
            <th class="px-3 py-2 text-left border-b">Comment</th>
        </tr>
    </thead>
    <tbody>
        @forelse($earnings as $earning)
        <tr>
            <td class="px-3 py-2 border-b">{{ $earning->created_at->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2 border-b">{{ ucfirst($earning->earning_type) }}</td>
            <td class="px-3 py-2 border-b font-semibold text-green-600">৳{{ number_format($earning->amount, 2) }}</td>
            <td class="px-3 py-2 border-b">
                @if($earning->order)
                    #{{ $earning->order->order_number }}
                @else
                    -
                @endif
            </td>
            <td class="px-3 py-2 border-b">
                @if($earning->referral)
                    {{ $earning->referral->name }}
                @else
                    -
                @endif
            </td>
            <td class="px-3 py-2 border-b">{{ $earning->comment }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-3 py-4 text-center text-neutral-500">No earnings yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $earnings->links() }}
</div>
@endsection


