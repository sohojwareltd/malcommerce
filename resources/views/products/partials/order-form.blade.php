@php
    /** @var \App\Models\Product $product */
    // Use product-specific settings with fallback to global settings
    $orderFormTitle = $product->order_form_title ?: \App\Models\Setting::get('order_form_title', 'অর্ডার করুন');
    $orderButtonText = $product->order_button_text ?: \App\Models\Setting::get('order_button_text', 'অর্ডার নিশ্চিত করুন');
    $hideSummary = $product->order_hide_summary ?? \App\Models\Setting::get('order_hide_summary', false);
    $hideQuantity = $product->order_hide_quantity ?? \App\Models\Setting::get('order_hide_quantity', false);
    $deliveryOptions = $product->order_delivery_options 
        ? json_decode($product->order_delivery_options, true) 
        : json_decode(\App\Models\Setting::get('order_delivery_options', '[]'), true);
    $minQuantity = (int) ($product->order_min_quantity ?: \App\Models\Setting::get('order_min_quantity', 0));
    $maxQuantity = (int) ($product->order_max_quantity ?: \App\Models\Setting::get('order_max_quantity', 0));
@endphp

<div id="order" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Product Info -->
    <div class="card">
        <div class="mb-4">
            @if($product->main_image)
            <img 
                src="{{ $product->main_image }}" 
                alt="{{ $product->name }}" 
                class="w-full h-64 object-cover rounded-lg mb-4"
            >
            @else
            <div class="w-full h-64 bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                <span class="text-gray-400 font-bangla">No Image</span>
            </div>
            @endif
        </div>
        <h2 class="text-2xl font-bold mb-3 text-gray-900 font-bangla">{{ $product->name }}</h2>
        @if($product->short_description)
        <p class="text-gray-700 font-bangla leading-relaxed">{{ $product->short_description }}</p>
        @endif
    </div>
    
    <!-- Order Form -->
    <div class="card">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 font-bangla">{{ $orderFormTitle }}</h2>

    <form action="{{ route('orders.store') }}" method="POST" x-data="{ 
        quantity: {{ max(1, $minQuantity) }}, 
        price: {{ $product->price }}, 
        deliveryCharge: 0,
        selectedDelivery: null,
        totalPrice: {{ ($product->price * max(1, $minQuantity)) }},
        minQuantity: {{ max(1, $minQuantity) }},
        maxQuantity: {{ min($product->stock_quantity ?? 999, $maxQuantity > 0 ? $maxQuantity : 999) }},
        updateTotal() {
            this.totalPrice = (this.quantity * this.price) + this.deliveryCharge;
        }
    }" @input="updateTotal()">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        {{-- Quantity Selector --}}
        @if(!$hideQuantity)
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3 font-bangla">
                পরিমাণ
            </label>
            <div class="flex items-center gap-3">
                <button 
                    type="button"
                    @click="if(quantity > minQuantity) { quantity--; updateTotal(); }"
                    class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                >
                    -
                </button>
                <input 
                    type="number"
                    name="quantity"
                    x-model.number="quantity"
                    @input="updateTotal()"
                    x-bind:min="minQuantity"
                    x-bind:max="maxQuantity"
                    class="w-20 text-center border border-gray-300 rounded-lg px-2 py-2 font-bold focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >
                <button 
                    type="button"
                    @click="if(quantity < maxQuantity) { quantity++; updateTotal(); }"
                    class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                >
                    +
                </button>
                <span class="text-gray-600 font-bangla text-sm">(স্টকে: {{ $product->stock_quantity ?? '∞' }} টি)</span>
            </div>
        </div>
        @else
        <input type="hidden" name="quantity" value="1" x-model="quantity">
        @endif

        {{-- Customer Information --}}
        <div class="space-y-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                    আপনার নাম <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text"
                    name="customer_name"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                    placeholder="আপনার সম্পূর্ণ নাম"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                    মোবাইল নাম্বার <span class="text-red-500">*</span>
                </label>
                <input 
                    type="tel"
                    name="customer_phone"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="01XXXXXXXXX"
                    pattern="[0-9]{11}"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                    পূর্ণ ঠিকানা <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="address"
                    rows="4"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla resize-none"
                    placeholder="বাড়ি নম্বর, রোড, এলাকা, জেলা"
                ></textarea>
            </div>
        </div>

        {{-- Delivery Options --}}
        @if(!empty($deliveryOptions))
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3 font-bangla">
                ডেলিভারি অপশন
            </label>
            <div class="space-y-2">
                @foreach($deliveryOptions as $index => $option)
                <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input 
                        type="radio" 
                        name="delivery_option" 
                        value="{{ $index }}"
                        x-model="selectedDelivery"
                        @change="deliveryCharge = {{ $option['charge'] ?? 0 }}; updateTotal();"
                        class="w-4 h-4 text-primary border-gray-300 focus:ring-primary"
                    >
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 font-bangla">{{ $option['name'] ?? 'Standard' }}</div>
                        <div class="text-sm text-gray-600 font-bangla">
                            ৳{{ number_format($option['charge'] ?? 0, 2) }}
                            @if(isset($option['days']))
                            - {{ $option['days'] }} দিন
                            @endif
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Order Summary --}}
        @if(!$hideSummary)
        <div class="bg-gray-50 rounded-xl p-6 mb-6 border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4 font-bangla">অর্ডার সারাংশ</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-bangla">পণ্যের মূল্য</span>
                    <span class="font-semibold text-gray-900">৳<span x-text="(price * quantity).toLocaleString('bn-BD')"></span></span>
                </div>
                @if(!$hideQuantity)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-bangla">পরিমাণ</span>
                    <span class="font-semibold text-gray-900"><span x-text="quantity"></span> টি</span>
                </div>
                @endif
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <span class="font-bangla">ডেলিভারি চার্জ</span>
                    <span class="font-bangla" x-text="deliveryCharge > 0 ? '৳' + deliveryCharge.toLocaleString('bn-BD') : 'ফ্রি'"></span>
                </div>
                <div class="border-t border-gray-300 pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900 font-bangla">মোট</span>
                        <span class="text-2xl font-bold" style="color: var(--color-primary);">৳<span x-text="totalPrice.toLocaleString('bn-BD')"></span></span>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-600 mt-4 font-bangla">
                💳 ক্যাশ অন ডেলিভারি - অগ্রীম কোন টাকা ছাড়াই অর্ডার করুন
            </p>
        </div>
        @endif

        <div x-show="totalPrice < {{ $minAmount }} && {{ $minAmount }} > 0" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800 font-bangla">
                সর্বনিম্ন অর্ডার পরিমাণ: ৳{{ number_format($minAmount, 2) }}
            </p>
        </div>
        <div x-show="totalPrice > {{ $maxAmount }} && {{ $maxAmount }} > 0" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800 font-bangla">
                সর্বোচ্চ অর্ডার পরিমাণ: ৳{{ number_format($maxAmount, 2) }}
            </p>
        </div>

        <button 
            type="submit"
            class="w-full btn-primary font-bangla text-lg py-4 rounded-xl shadow-lg hover:shadow-xl"
            style="background-color: var(--color-primary);"
            :disabled="(totalPrice < {{ $minAmount }} && {{ $minAmount }} > 0) || (totalPrice > {{ $maxAmount }} && {{ $maxAmount }} > 0)"
            :class="(totalPrice < {{ $minAmount }} && {{ $minAmount }} > 0) || (totalPrice > {{ $maxAmount }} && {{ $maxAmount }} > 0) ? 'opacity-50 cursor-not-allowed' : ''"
        >
            <span>{{ $orderButtonText }} - ৳<span x-text="totalPrice.toLocaleString('bn-BD')"></span></span>
        </button>
    </form>
    </div>
</div>
