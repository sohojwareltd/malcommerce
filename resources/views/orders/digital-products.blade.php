@extends('layouts.app')

@section('title', 'My Digital Products')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
    <div class="mb-6 sm:mb-8 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 font-bangla">আমার ডিজিটাল পণ্য</h1>
            <p class="text-sm sm:text-base text-neutral-600 mt-1 font-bangla">
                আপনি যে সব ডিজিটাল পণ্য ক্রয় করেছেন এবং বর্তমানে অ্যাক্সেস করতে পারেন, সেগুলো এখানে দেখুন।
            </p>
        </div>
        <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center gap-2 text-sm text-primary hover:underline font-bangla">
            হোমে ফিরুন
            <span aria-hidden="true">→</span>
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="bg-neutral-50 border border-dashed border-neutral-300 rounded-xl p-8 text-center">
            <p class="text-neutral-700 font-bangla mb-2">এখনও কোনো ডিজিটাল পণ্যের অ্যাক্সেস নেই।</p>
            <p class="text-sm text-neutral-500 font-bangla mb-4">
                ডিজিটাল পণ্য কিনলে বা অ্যাক্সেস পেলে সেগুলো এখানে দেখা যাবে।
            </p>
            <a href="{{ route('products.index', ['type' => 'digital']) }}"
               class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-medium font-bangla hover:bg-primary-light transition">
                ডিজিটাল পণ্য দেখুন
            </a>
        </div>
    @else
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
            <div class="border-b border-neutral-200 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between">
                <h2 class="text-sm sm:text-base font-semibold text-neutral-800 font-bangla">
                    মোট {{ $orders->total() }}টি ডিজিটাল অর্ডার
                </h2>
            </div>

            <div class="divide-y divide-neutral-100">
                @foreach($orders as $order)
                    <div class="px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 text-sm">
                                <span class="font-mono text-xs sm:text-sm font-semibold text-neutral-800">
                                    #{{ $order->order_number }}
                                </span>
                                <span class="text-xs text-neutral-400">•</span>
                                <span class="font-bangla text-neutral-700">
                                    {{ optional($order->product)->name ?? 'পণ্য পাওয়া যাচ্ছে না' }}
                                </span>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs sm:text-sm text-neutral-500">
                                <span class="font-bangla">
                                    {{ $order->created_at?->format('d M, Y h:i A') }}
                                </span>
                                <span>•</span>
                                <span class="font-bangla">
                                    পরিমাণ: {{ $order->quantity }},
                                    মোট: ৳{{ number_format($order->total_price, 0) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            @if($order->canAccessDigitalContent() && $order->product)
                                @if($order->product->hasDigitalFile())
                                    <a href="{{ route('orders.digital.download', $order) }}"
                                       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg bg-primary text-white text-xs sm:text-sm font-medium font-bangla hover:bg-primary-light transition">
                                        ফাইল ডাউনলোড করুন
                                    </a>
                                @endif
                                @if($order->product->hasDigitalLink())
                                    <a href="{{ route('orders.digital.link', $order) }}"
                                       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg border border-primary text-primary text-xs sm:text-sm font-medium font-bangla hover:bg-primary/5 transition">
                                        লিংক/টেক্সট দেখুন
                                    </a>
                                @endif
                            @else
                                <span class="inline-flex items-center px-3 sm:px-4 py-1.5 rounded-full bg-amber-50 text-amber-700 text-xs sm:text-xs font-medium font-bangla">
                                    প্রসেসিং বা পেমেন্ট সম্পন্ন হওয়ার পর কন্টেন্ট পাওয়া যাবে
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($orders->hasPages())
                <div class="border-t border-neutral-200 px-4 sm:px-6 py-3">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

