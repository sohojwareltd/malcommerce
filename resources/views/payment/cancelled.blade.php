@extends('layouts.app')

@section('title', 'Payment cancelled')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 text-amber-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-amber-600 mb-3 font-bangla">পেমেন্ট বাতিল</h1>
        <p class="text-neutral-600 mb-6 font-bangla">{{ $message }}</p>

        @if(!empty($orderNumber))
        <div class="bg-neutral-50 rounded-lg p-4 mb-6 text-left">
            <p class="text-sm font-bangla text-neutral-700">
                <strong>অর্ডার নম্বর:</strong> {{ $orderNumber }}
            </p>
            <p class="text-xs text-neutral-500 mt-2 font-bangla">
                এই অর্ডারটি সিস্টেম থেকে সরানো হয়েছে। আবার অর্ডার করতে পারেন।
            </p>
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition font-bangla">
                হোমে ফিরে যান
            </a>
            <a href="{{ route('products.index') }}" class="inline-block border border-neutral-300 text-neutral-800 px-6 py-3 rounded-lg font-semibold hover:bg-neutral-50 transition font-bangla">
                পণ্য দেখুন
            </a>
        </div>
    </div>
</div>
@endsection
