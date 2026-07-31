@extends('layouts.admin')

@section('title', 'Exam Orders')

@section('content')
<h1 class="mb-6 text-2xl font-bold">Exam orders</h1>
<div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 text-left"><tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Exam</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y">
            @foreach($orders as $order)
            <tr>
                <td class="px-4 py-3 font-mono">{{ $order->order_number }}</td>
                <td class="px-4 py-3">{{ $order->exam->title }}</td>
                <td class="px-4 py-3">{{ $order->customer_name }}<br><span class="text-xs text-neutral-500">{{ $order->customer_phone }}</span></td>
                <td class="px-4 py-3">৳{{ number_format($order->total_price, 2) }}</td>
                <td class="px-4 py-3">{{ $order->payment_status }}</td>
                <td class="px-4 py-3"><a href="{{ route('admin.exam-orders.show', $order) }}" class="text-primary">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
