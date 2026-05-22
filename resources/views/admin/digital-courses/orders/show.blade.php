@extends('layouts.admin')

@section('title', 'Course Order')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.digital-course-orders.index') }}" class="text-primary hover:underline text-sm font-semibold">← Course orders</a>
    <h1 class="text-2xl font-bold mt-2">Order {{ $order->order_number }}</h1>
</div>

@if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6 space-y-3 text-sm">
        <h2 class="font-bold text-lg">Order</h2>
        <p><span class="text-neutral-500">Course:</span> {{ $order->course?->title }}</p>
        <p><span class="text-neutral-500">Amount:</span> ৳{{ number_format($order->total_price, 2) }}</p>
        <p><span class="text-neutral-500">Status:</span> {{ $order->status }}</p>
        <p><span class="text-neutral-500">Payment:</span> {{ $order->payment_status }}</p>
        @if($order->payment_completed_at)<p><span class="text-neutral-500">Paid at:</span> {{ $order->payment_completed_at->format('M d, Y H:i') }}</p>@endif
        @if($order->payment_transaction_id)<p><span class="text-neutral-500">Trx:</span> <span class="font-mono text-xs">{{ $order->payment_transaction_id }}</span></p>@endif
    </div>
    <div class="bg-white rounded-lg shadow p-6 space-y-3 text-sm">
        <h2 class="font-bold text-lg">Customer</h2>
        <p><span class="text-neutral-500">Name:</span> {{ $order->customer_name }}</p>
        <p><span class="text-neutral-500">Phone:</span> {{ $order->customer_phone }}</p>
        @if($order->user)
            <p><span class="text-neutral-500">Account:</span> {{ $order->user->name }} ({{ $order->user->role }}) #{{ $order->user->id }}</p>
        @endif
        <p><span class="text-neutral-500">Enrollment:</span> {{ $order->enrollment ? 'Granted ' . $order->enrollment->granted_at->format('M d, Y') : 'Not granted' }}</p>
        @if($order->payment_status !== 'completed')
        <form method="POST" action="{{ route('admin.digital-course-orders.mark-paid', $order) }}" onsubmit="return confirm('Mark as paid and grant access?');">
            @csrf
            <button type="submit" class="mt-2 bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Mark paid &amp; enroll</button>
        </form>
        @endif
    </div>
</div>
@endsection
