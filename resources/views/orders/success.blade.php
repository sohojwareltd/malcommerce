@extends('layouts.app')

@section('title', 'Order Success')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 text-accent mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold mb-4">Order Placed Successfully!</h1>
        <p class="text-neutral-600 mb-6">Thank you for your order. We'll process it shortly.</p>
        
        <div class="bg-neutral-50 rounded-lg p-6 mb-6 text-left">
            <h2 class="font-semibold mb-4">Order Details</h2>
            <div class="space-y-2 text-sm">
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Product:</strong> {{ $order->product->name }}</p>
                <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                <p><strong>Total:</strong> ৳{{ number_format($order->total_price, 2) }}</p>
                <p><strong>Status:</strong> <span class="capitalize">{{ $order->status }}</span></p>
            </div>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection


