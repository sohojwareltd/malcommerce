@extends('layouts.admin')

@section('title', 'Withdrawal Requests')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Withdrawal Requests</h1>
</div>

<form method="GET" class="mb-4 flex items-center gap-2">
    <label class="text-sm">Status:</label>
    <select name="status" class="border border-neutral-300 rounded-lg px-2 py-1 text-sm">
        <option value="">All</option>
        @foreach(['pending','processing','approved','cancelled','inquiry'] as $status)
            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                {{ ucfirst($status) }}
            </option>
        @endforeach
    </select>
    <button class="bg-primary text-white px-3 py-1 rounded text-sm">Filter</button>
</form>

<table class="min-w-full bg-white border border-neutral-200 text-sm">
    <thead>
        <tr class="bg-neutral-100">
            <th class="px-3 py-2 text-left border-b">ID</th>
            <th class="px-3 py-2 text-left border-b">Sponsor</th>
            <th class="px-3 py-2 text-left border-b">Amount</th>
            <th class="px-3 py-2 text-left border-b">Status</th>
            <th class="px-3 py-2 text-left border-b">Requested At</th>
            <th class="px-3 py-2 text-left border-b">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($withdrawals as $withdrawal)
        <tr>
            <td class="px-3 py-2 border-b">#{{ $withdrawal->id }}</td>
            <td class="px-3 py-2 border-b">
                {{ $withdrawal->sponsor->name ?? 'N/A' }}<br>
                <span class="text-xs text-neutral-500">{{ $withdrawal->sponsor->phone ?? '' }}</span>
            </td>
            <td class="px-3 py-2 border-b font-semibold">৳{{ number_format($withdrawal->amount, 2) }}</td>
            <td class="px-3 py-2 border-b">{{ ucfirst($withdrawal->status) }}</td>
            <td class="px-3 py-2 border-b">{{ $withdrawal->requested_at?->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2 border-b">
                <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="text-primary text-xs">View</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-3 py-4 text-center text-neutral-500">No withdrawal requests found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $withdrawals->links() }}
</div>
@endsection


