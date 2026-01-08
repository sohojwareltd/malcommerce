@extends('layouts.admin')

@section('title', 'Edit Order')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Edit Order</h1>
        <p class="text-neutral-600 mt-1">Order #{{ $order->order_number }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.orders.show', $order) }}" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
            ← Back to Order
        </a>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
            ← Back to Orders
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Selection -->
            <div class="md:col-span-2">
                <label for="product_id" class="block text-sm font-medium text-neutral-700 mb-2">Product *</label>
                <select name="product_id" id="product_id" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select a product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id', $order->product_id) == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} - ৳{{ number_format($product->price, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-neutral-700 mb-2">Quantity *</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $order->quantity) }}" required min="1" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unit Price -->
            <div>
                <label for="unit_price" class="block text-sm font-medium text-neutral-700 mb-2">Unit Price (৳) *</label>
                <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price', $order->unit_price) }}" required min="0" step="0.01" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('unit_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Delivery Charge -->
            <div>
                <label for="delivery_charge" class="block text-sm font-medium text-neutral-700 mb-2">Delivery Charge (৳)</label>
                <input type="number" name="delivery_charge" id="delivery_charge" value="{{ old('delivery_charge', $order->delivery_charge ?? 0) }}" min="0" step="0.01" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('delivery_charge')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Price (Read-only display) -->
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Total Price (৳)</label>
                <div id="total-price-display" class="px-4 py-2 bg-neutral-50 border border-neutral-300 rounded-lg text-lg font-semibold text-green-600">
                    ৳{{ number_format(($order->unit_price * $order->quantity) + ($order->delivery_charge ?? 0), 2) }}
                </div>
                <p class="mt-1 text-xs text-neutral-500">Calculated automatically: (Unit Price × Quantity) + Delivery Charge</p>
            </div>

            <!-- Customer Name -->
            <div class="md:col-span-2">
                <label for="customer_name" class="block text-sm font-medium text-neutral-700 mb-2">Customer Name *</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('customer_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer Phone -->
            <div>
                <label for="customer_phone" class="block text-sm font-medium text-neutral-700 mb-2">Customer Phone *</label>
                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('customer_phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address *</label>
                <textarea name="address" id="address" rows="4" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('address', $order->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition font-semibold">
                Update Order
            </button>
            <a href="{{ route('admin.orders.show', $order) }}" class="px-6 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const deliveryChargeInput = document.getElementById('delivery_charge');
    const totalPriceDisplay = document.getElementById('total-price-display');

    function updateTotalPrice() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const deliveryCharge = parseFloat(deliveryChargeInput.value) || 0;
        const total = (quantity * unitPrice) + deliveryCharge;
        totalPriceDisplay.textContent = '৳' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    quantityInput.addEventListener('input', updateTotalPrice);
    unitPriceInput.addEventListener('input', updateTotalPrice);
    deliveryChargeInput.addEventListener('input', updateTotalPrice);
});
</script>
@endsection
