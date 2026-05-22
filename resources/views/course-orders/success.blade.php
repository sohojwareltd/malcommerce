@extends('layouts.app')

@section('title', 'কোর্স অর্ডার সফল')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        @if(session('success') || $order->payment_status === 'completed')
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h1 class="text-2xl font-bold text-green-600 mb-2 font-bangla">পেমেন্ট সফল!</h1>
        @else
            <h1 class="text-2xl font-bold text-gray-900 mb-2 font-bangla">অর্ডার তৈরি হয়েছে</h1>
        @endif

        <p class="text-gray-600 mb-6 font-bangla text-sm">অর্ডার #{{ $order->order_number }}</p>

        <div class="bg-gray-50 rounded-lg p-4 text-left text-sm mb-6 space-y-2">
            <p><strong>কোর্স:</strong> {{ $order->course->title }}</p>
            <p><strong>মোট:</strong> ৳{{ number_format($order->total_price, 2) }}</p>
            <p><strong>পেমেন্ট:</strong> {{ $order->payment_status }}</p>
        </div>

        @if($enrolled || $order->payment_status === 'completed')
            @auth
                <a href="{{ route('my-courses.show', $order->course) }}" class="inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold font-bangla mb-3">কোর্স দেখুন</a>
            @else
                <p class="text-gray-700 mb-4 font-bangla text-sm">একই মোবাইল নম্বর দিয়ে লগইন করুন ভিডিও দেখতে।</p>
                <a href="{{ route('login') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold font-bangla">লগইন করুন</a>
            @endauth
        @elseif($order->payment_status === 'processing')
            <p class="text-amber-700 text-sm font-bangla mb-4" id="payment-wait-msg">পেমেন্ট নিশ্চিত হচ্ছে...</p>
            <script>
                (function poll() {
                    fetch('{{ route('payment.course-check-status') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ order_id: {{ $order->id }} }),
                    }).then(r => r.json()).then(d => {
                        if (d.payment_status === 'completed') location.reload();
                        else setTimeout(poll, 3000);
                    }).catch(() => setTimeout(poll, 5000));
                })();
            </script>
        @endif

        <a href="{{ route('courses.index') }}" class="block mt-4 text-primary text-sm font-bangla">← সব কোর্স</a>
    </div>
</div>
@endsection
