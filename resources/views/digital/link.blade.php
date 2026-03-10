@extends('layouts.app')

@section('title', 'Your Digital Content')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-neutral-900 mb-2">{{ $product->name }}</h1>
        <p class="text-neutral-600 text-sm mb-6">Order #{{ $order->order_number }}</p>

        <label class="block text-sm font-medium text-neutral-700 mb-2">Your link / content</label>
        <div class="relative">
            <textarea id="digital-link-text" readonly rows="6" class="w-full px-4 py-3 border border-neutral-300 rounded-lg bg-neutral-50 text-neutral-800 font-mono text-sm">{{ $digitalLinkText }}</textarea>
            <button type="button" onclick="copyLink()" class="mt-3 w-full sm:w-auto bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
                Copy to clipboard
            </button>
        </div>
        <p class="text-xs text-neutral-500 mt-3">You can paste this link in your browser or share it as needed.</p>

        <div class="mt-8 pt-6 border-t border-neutral-200">
            <a href="{{ route('sponsor.orders.my-orders') }}" class="text-primary font-medium hover:underline">← Back to My Orders</a>
        </div>
    </div>
</div>

<script>
function copyLink() {
    const el = document.getElementById('digital-link-text');
    el.select();
    el.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(el.value).then(function() {
        const btn = document.querySelector('button[onclick="copyLink()"]');
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.disabled = true;
        setTimeout(function() { btn.textContent = orig; btn.disabled = false; }, 2000);
    });
}
</script>
@endsection
