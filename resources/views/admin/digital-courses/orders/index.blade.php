@extends('layouts.admin')

@section('title', 'Course Orders')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold">Course orders</h1>
    <p class="text-neutral-600 mt-1 text-sm">bKash purchases for digital courses</p>
</div>

@if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>@endif

<form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, name, phone" class="flex-1 min-w-[180px] rounded-lg border-neutral-300 text-sm">
    <select name="payment_status" class="rounded-lg border-neutral-300 text-sm">
        <option value="">All payments</option>
        @foreach(['pending','processing','completed','failed','cancelled'] as $s)
            <option value="{{ $s }}" @selected(request('payment_status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">Filter</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full text-sm divide-y divide-neutral-200">
        <thead class="bg-neutral-50">
            <tr>
                <th class="px-4 py-3 text-left">Order</th>
                <th class="px-4 py-3 text-left">Course</th>
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-right">Amount</th>
                <th class="px-4 py-3 text-left">Payment</th>
                <th class="px-4 py-3 text-left">Enrollment</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
            @forelse($orders as $order)
            <tr>
                <td class="px-4 py-3 font-mono text-xs">{{ $order->order_number }}</td>
                <td class="px-4 py-3">{{ $order->course?->title }}</td>
                <td class="px-4 py-3">
                    <div class="font-medium">{{ $order->customer_name }}</div>
                    <div class="text-xs text-neutral-500">{{ $order->customer_phone }}</div>
                </td>
                <td class="px-4 py-3 text-right font-semibold">৳{{ number_format($order->total_price, 0) }}</td>
                <td class="px-4 py-3"><span class="text-xs font-semibold">{{ $order->payment_status }}</span></td>
                <td class="px-4 py-3">{{ $order->enrollment ? 'Yes' : '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.digital-course-orders.show', $order) }}" class="text-primary font-semibold hover:underline">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-neutral-500">No orders.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($orders->hasPages())<div class="px-4 py-3">{{ $orders->links() }}</div>@endif
</div>
@endsection
