@extends('layouts.app')

@section('title', 'Order Success')

@push('scripts')
@if(\App\Models\Setting::get('fb_pixel_id'))
<script>
  if (typeof fbq === 'function') {
    fbq('track', 'Purchase', {
      value: {{ (float) $order->total_price }},
      currency: 'BDT',
      order_id: @json($order->order_number),
      content_ids: [@json($order->product_id)],
      content_type: 'product',
      content_name: @json($order->product->name),
      content_category: @json(optional($order->product->category)->name),
      num_items: {{ (int) $order->quantity }}
    });
  }
</script>
@endif
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-green-600 mb-4">Order Placed Successfully!</h1>
        <p class="text-neutral-600 mb-6">Thank you for your order. We'll process it shortly.</p>
        
        <div class="bg-neutral-50 rounded-lg p-6 mb-6 text-left">
            <h2 class="font-semibold mb-4 font-bangla">অর্ডার বিবরণ</h2>
            <div class="space-y-2 text-sm">
                <p class="font-bangla"><strong>অর্ডার নম্বর:</strong> {{ $order->order_number }}</p>
                <p class="font-bangla"><strong>পণ্য:</strong> {{ $order->product->name }}</p>
                <p class="font-bangla"><strong>পরিমাণ:</strong> {{ $order->quantity }}</p>
                <p class="font-bangla"><strong>মোট:</strong> ৳{{ number_format($order->total_price, 2) }}</p>
                <p class="font-bangla"><strong>অর্ডার স্ট্যাটাস:</strong> <span class="capitalize">
                    @if($order->status === 'pending') পেন্ডিং
                    @elseif($order->status === 'processing') প্রসেসিং
                    @elseif($order->status === 'shipped') শিপড
                    @elseif($order->status === 'delivered') ডেলিভার্ড
                    @elseif($order->status === 'cancelled') বাতিল
                    @else {{ $order->status }}
                    @endif
                </span></p>
                <p class="font-bangla"><strong>পেমেন্ট পদ্ধতি:</strong> 
                    @if($order->payment_method === 'cod')
                        <span>💵 ক্যাশ অন ডেলিভারি</span>
                    @elseif($order->payment_method === 'bkash')
                        <span>📱 bKash</span>
                    @else
                        {{ $order->payment_method }}
                    @endif
                </p>
                @if($order->payment_method === 'bkash')
                <p class="font-bangla"><strong>পেমেন্ট স্ট্যাটাস:</strong> 
                    <span class="capitalize">
                        @if($order->payment_status === 'pending')
                            <span class="text-yellow-600 font-semibold">⏳ পেন্ডিং</span>
                        @elseif($order->payment_status === 'processing')
                            <span class="text-blue-600 font-semibold">🔄 প্রসেসিং</span>
                        @elseif($order->payment_status === 'completed')
                            <span class="text-green-600 font-semibold">✅ সম্পন্ন</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="text-red-600 font-semibold">❌ ব্যর্থ</span>
                        @elseif($order->payment_status === 'cancelled')
                            <span class="text-gray-600 font-semibold">🚫 বাতিল</span>
                        @else
                            {{ $order->payment_status }}
                        @endif
                    </span>
                </p>
                @if($order->payment_status === 'pending' || $order->payment_status === 'processing')
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800 font-bangla mb-2">
                        আপনার bKash পেমেন্ট সম্পন্ন করুন অথবা পেমেন্ট স্ট্যাটাস চেক করুন।
                    </p>
                    <button 
                        onclick="checkPaymentStatus({{ $order->id }})"
                        class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-bangla"
                    >
                        পেমেন্ট স্ট্যাটাস চেক করুন
                    </button>
                </div>
                @endif
                @endif
            </div>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition font-bangla">
                শপিং চালিয়ে যান
            </a>
        </div>
    </div>
</div>

@if($order->payment_method === 'bkash' && ($order->payment_status === 'pending' || $order->payment_status === 'processing'))
@push('scripts')
<script>
function checkPaymentStatus(orderId) {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'চেক করা হচ্ছে...';
    button.disabled = true;

    fetch('{{ route("payment.check-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order_id: orderId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.payment_status === 'completed') {
                alert('পেমেন্ট সফল হয়েছে!');
                location.reload();
            } else {
                alert('পেমেন্ট এখনও সম্পন্ন হয়নি। দয়া করে bKash অ্যাপ/ওয়েবসাইটে পেমেন্ট সম্পন্ন করুন।');
            }
        } else {
            alert('পেমেন্ট স্ট্যাটাস চেক করতে ব্যর্থ: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('পেমেন্ট স্ট্যাটাস চেক করতে ব্যর্থ হয়েছে।');
    })
    .finally(() => {
        button.textContent = originalText;
        button.disabled = false;
    });
}
</script>
@endpush
@endif
@endsection


