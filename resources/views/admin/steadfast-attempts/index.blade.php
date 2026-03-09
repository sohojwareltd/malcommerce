@extends('layouts.admin')

@section('title', 'Steadfast Attempts')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Steadfast Attempts</h1>
        <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Webhook and API call logs for Steadfast courier integration</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.steadfast-attempts.index') }}" class="flex flex-col sm:flex-row gap-4 sm:items-end flex-wrap">
        <div class="sm:w-40">
            <label for="type" class="block text-sm font-medium text-neutral-700 mb-1">Type</label>
            <select name="type" id="type" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="webhook" {{ request('type') === 'webhook' ? 'selected' : '' }}>Webhook</option>
                <option value="create_order" {{ request('type') === 'create_order' ? 'selected' : '' }}>Create Order</option>
                <option value="get_status" {{ request('type') === 'get_status' ? 'selected' : '' }}>Get Status</option>
                <option value="get_balance" {{ request('type') === 'get_balance' ? 'selected' : '' }}>Get Balance</option>
            </select>
        </div>
        <div class="sm:w-40">
            <label for="success" class="block text-sm font-medium text-neutral-700 mb-1">Result</label>
            <select name="success" id="success" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="1" {{ request('success') === '1' ? 'selected' : '' }}>Success</option>
                <option value="0" {{ request('success') === '0' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div class="sm:w-40">
            <label for="from" class="block text-sm font-medium text-neutral-700 mb-1">From</label>
            <input type="date" name="from" id="from" value="{{ request('from') }}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="sm:w-40">
            <label for="to" class="block text-sm font-medium text-neutral-700 mb-1">To</label>
            <input type="date" name="to" id="to" value="{{ request('to') }}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="sm:w-48">
            <label for="search" class="block text-sm font-medium text-neutral-700 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Order #, IP, message…" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
            <a href="{{ route('admin.steadfast-attempts.index') }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-300">Clear</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Result</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">IP / Info</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($attempts as $attempt)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">{{ $attempt->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-medium">{{ $attempt->type }}</span>
                        @if($attempt->notification_type)
                            <span class="text-neutral-500">({{ $attempt->notification_type }})</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($attempt->order)
                            <a href="{{ route('admin.orders.show', $attempt->order) }}" class="text-primary hover:underline">#{{ $attempt->order->order_number }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($attempt->success)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Success</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Failed</span>
                        @endif
                        @if($attempt->http_status)
                            <span class="text-neutral-500 ml-1">{{ $attempt->http_status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-600 max-w-[200px] truncate" title="{{ $attempt->error_message ?? $attempt->ip_address }}">
                        {{ $attempt->ip_address ?? $attempt->error_message ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <a href="{{ route('admin.steadfast-attempts.show', $attempt) }}" class="text-primary hover:text-primary-light font-medium">Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-neutral-500">No Steadfast attempts recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($attempts->hasPages())
<div class="mt-4">
    {{ $attempts->links() }}
</div>
@endif
@endsection
