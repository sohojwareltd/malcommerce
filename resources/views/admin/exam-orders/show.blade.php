@extends('layouts.admin')

@section('title', 'Exam Order')

@section('content')
<h1 class="mb-6 text-2xl font-bold">Order {{ $examOrder->order_number }}</h1>
<div class="max-w-2xl rounded-xl border bg-white p-6 shadow-sm space-y-3 text-sm">
    <p><strong>Exam:</strong> {{ $examOrder->exam->title }}</p>
    <p><strong>Customer:</strong> {{ $examOrder->customer_name }} ({{ $examOrder->customer_phone }})</p>
    <p><strong>Total:</strong> ৳{{ number_format($examOrder->total_price, 2) }}</p>
    <p><strong>Payment:</strong> {{ $examOrder->payment_status }} / {{ $examOrder->status }}</p>
    @if(!$examOrder->isPaid())
    <form method="POST" action="{{ route('admin.exam-orders.mark-paid', $examOrder) }}">@csrf<button class="rounded-lg bg-green-600 px-4 py-2 text-white text-sm">Mark paid & grant access</button></form>
    @endif
</div>
@endsection
