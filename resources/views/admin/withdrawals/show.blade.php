@extends('layouts.admin')

@section('title', 'Withdrawal #' . $withdrawal->id)

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Withdrawal #{{ $withdrawal->id }}</h1>
    <p class="text-sm text-neutral-600 mt-1">
        Sponsor: {{ $withdrawal->sponsor->name }} ({{ $withdrawal->sponsor->phone }})<br>
        Amount: ৳{{ number_format($withdrawal->amount, 2) }}<br>
        Status: <strong>{{ ucfirst($withdrawal->status) }}</strong>
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white border border-neutral-200 rounded-lg p-4 text-sm">
        <h2 class="font-semibold mb-2">Method Details</h2>
        @php
            $info = $withdrawal->receiving_account_information ?? [];
            $number = $info['number'] ?? null;
            $masked = $number ? substr($number, 0, 3) . 'XXX-XXXXX' : '-';
        @endphp
        <p class="text-sm">
            <strong>Provider:</strong> {{ strtoupper($info['provider'] ?? '-') }}<br>
            <strong>Mobile:</strong> {{ $masked }}<br>
            <strong>Account Type:</strong> {{ ucfirst($info['account_type'] ?? '-') }}<br>
            <strong>Account Holder:</strong> {{ $info['holder_name'] ?? '-' }}<br>
            <strong>Label:</strong> {{ $info['label'] ?? '-' }}
        </p>
    </div>

    <div class="bg-white border border-neutral-200 rounded-lg p-4 text-sm">
        <h2 class="font-semibold mb-2">Notes & Actions</h2>
        <form action="{{ route('admin.withdrawals.update', $withdrawal) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Action</label>
                <select name="action" class="w-full border border-neutral-300 rounded px-2 py-1 text-sm">
                    <option value="approve">Approve</option>
                    <option value="cancel">Cancel</option>
                    <option value="inquiry">Mark as Inquiry</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Admin Note</label>
                <textarea name="admin_note" rows="2" class="w-full border border-neutral-300 rounded px-2 py-1 text-sm">{{ old('admin_note', $withdrawal->admin_note) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-neutral-700 mb-1">Inquiry Note (for sponsor)</label>
                <textarea name="inquiry_note" rows="2" class="w-full border border-neutral-300 rounded px-2 py-1 text-sm">{{ old('inquiry_note', $withdrawal->inquiry_note) }}</textarea>
            </div>

            <button type="submit" class="bg-primary text-white px-4 py-2 rounded text-sm font-semibold">Update Withdrawal</button>
        </form>
    </div>
</div>
@endsection


