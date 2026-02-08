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
    
    // Get allowed payment methods for this product
    $allowedPaymentMethods = $product->getAllowedPaymentMethods();
    // Ensure we always have at least one payment method
    if (empty($allowedPaymentMethods) || !is_array($allowedPaymentMethods)) {
        $allowedPaymentMethods = ['cod', 'bkash'];
    }
    $defaultPaymentMethod = !empty($allowedPaymentMethods) ? $allowedPaymentMethods[0] : 'cod';
@endphp

<div id="order" class="grid grid-cols-1  gap-6">
    <!-- Product Info -->

    
    <!-- Order Form -->
    <div class="card overflow-hidden">
    <h2 class="text-xl md:text-2xl font-bold mb-6 text-gray-900 font-bangla break-words">{{ $orderFormTitle }}</h2>

    <form action="{{ route('orders.store') }}" method="POST" x-data="{ 
        quantity: {{ max(1, $minQuantity) }}, 
        price: {{ $product->price }}, 
        deliveryCharge: {{ !empty($deliveryOptions) && isset($deliveryOptions[0]['charge']) ? $deliveryOptions[0]['charge'] : 0 }},
        selectedDelivery: {{ !empty($deliveryOptions) ? "'0'" : 'null' }},
        paymentMethod: '{{ $defaultPaymentMethod }}',
        totalPrice: {{ ($product->price * max(1, $minQuantity)) + (!empty($deliveryOptions) && isset($deliveryOptions[0]['charge']) ? $deliveryOptions[0]['charge'] : 0) }},
        minQuantity: {{ max(1, $minQuantity) }},
        maxQuantity: {{ min($product->stock_quantity ?? 999, $maxQuantity > 0 ? $maxQuantity : 999) }},
        hasDeliveryOptions: {{ !empty($deliveryOptions) ? 'true' : 'false' }},
        updateTotal() {
            this.totalPrice = (this.quantity * this.price) + this.deliveryCharge;
        },
        canSubmit() {
            if (this.quantity < this.minQuantity && this.minQuantity > 0) return false;
            if (this.quantity > this.maxQuantity && this.maxQuantity > 0) return false;
            if (this.hasDeliveryOptions && (this.selectedDelivery == null || this.selectedDelivery === undefined || this.selectedDelivery === '')) return false;
            return true;
        },
        init() {
            // Ensure first delivery option is selected and total is updated on initialization
            if (this.hasDeliveryOptions) {
                this.selectedDelivery = '0';
                this.deliveryCharge = {{ !empty($deliveryOptions) && isset($deliveryOptions[0]['charge']) ? $deliveryOptions[0]['charge'] : 0 }};
                this.updateTotal();
            }
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
            <div class="flex items-center gap-2 md:gap-4 flex-wrap">
                {{-- Product Image and Title --}}
                <div class="flex items-center gap-2 md:gap-3 flex-1 min-w-0">
                    @if($product->main_image)
                        <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-14 h-14 md:w-20 md:h-20 rounded-lg object-cover border border-gray-200 shadow-sm flex-shrink-0">
                    @else
                        <div class="w-14 h-14 md:w-20 md:h-20 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 md:w-10 md:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0 overflow-hidden">
                        <h3 class="font-semibold text-gray-900 font-bangla text-sm md:text-base truncate">{{ $product->name }}</h3>
                        <p class="text-xs md:text-sm text-gray-600 font-bangla whitespace-nowrap">৳{{ number_format($product->price, 2) }}</p>
                    </div>
                </div>
                
                {{-- Quantity Controls --}}
                <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                    <button 
                        type="button"
                        @click="if(quantity > minQuantity) { quantity--; updateTotal(); }"
                        class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
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
                        class="w-16 md:w-20 text-center border border-gray-300 rounded-lg px-1 md:px-2 py-2 font-bold focus:ring-2 focus:ring-primary focus:border-transparent text-sm md:text-base"
                        required
                    >
                    <button 
                        type="button"
                        @click="if(quantity < maxQuantity) { quantity++; updateTotal(); }"
                        class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                    >
                        +
                    </button>
                </div>
                <span class="text-gray-600 font-bangla text-xs md:text-sm hidden md:inline whitespace-nowrap">(স্টকে: {{ $product->stock_quantity ?? '∞' }} টি)</span>
            </div>
            <div class="mt-2 md:hidden">
                <span class="text-gray-600 font-bangla text-sm">স্টকে: {{ $product->stock_quantity ?? '∞' }} টি</span>
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
                    placeholder=""
                ></textarea>
            </div>
        </div>

        {{-- Delivery Options --}}
        @if(!empty($deliveryOptions))
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3 font-bangla">
                ডেলিভারি অপশন <span class="text-red-500">*</span>
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
                        required
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
            <div x-show="hasDeliveryOptions && (selectedDelivery == null || selectedDelivery === '' || selectedDelivery === undefined)" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800 font-bangla">
                    দয়া করে একটি ডেলিভারি অপশন নির্বাচন করুন
                </p>
            </div>
        </div>
        @endif

        {{-- Payment Method Selection (only show when more than one option) --}}
        @if(count($allowedPaymentMethods) > 1)
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3 font-bangla">
                পেমেন্ট পদ্ধতি <span class="text-red-500">*</span>
            </label>
            <div class="space-y-2">
                @if(in_array('cod', $allowedPaymentMethods))
                <label class="flex items-center gap-3 p-4 border-2 border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition" :class="paymentMethod === 'cod' ? 'border-primary bg-primary/5' : ''">
                    <input 
                        type="radio" 
                        name="payment_method" 
                        value="cod"
                        x-model="paymentMethod"
                        class="w-4 h-4 text-primary border-gray-300 focus:ring-primary"
                    >
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 font-bangla flex items-center gap-2">
                            <span>💵</span>
                            <span>ক্যাশ অন ডেলিভারি (COD)</span>
                        </div>
                        <div class="text-sm text-gray-600 font-bangla mt-1">
                            ডেলিভারির সময় পেমেন্ট করুন
                        </div>
                    </div>
                </label>
                @endif
                @if(in_array('bkash', $allowedPaymentMethods))
                <label class="flex items-center gap-3 p-4 border-2 border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition" :class="paymentMethod === 'bkash' ? 'border-primary bg-primary/5' : ''">
                    <input 
                        type="radio" 
                        name="payment_method" 
                        value="bkash"
                        x-model="paymentMethod"
                        class="w-4 h-4 text-primary border-gray-300 focus:ring-primary"
                    >
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 font-bangla flex items-center gap-2">
                            <span>📱</span>
                            <span>bKash</span>
                        </div>
                        <div class="text-sm text-gray-600 font-bangla mt-1">
                            bKash অ্যাপ/ওয়েবসাইটের মাধ্যমে পেমেন্ট করুন
                        </div>
                    </div>
                </label>
                @endif
            </div>
        </div>
        @else
        <input type="hidden" name="payment_method" value="{{ $defaultPaymentMethod }}">
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
            <p class="text-xs text-gray-600 mt-4 font-bangla" x-show="paymentMethod === 'cod'">
                💳 ক্যাশ অন ডেলিভারি - অগ্রীম কোন টাকা ছাড়াই অর্ডার করুন
            </p>
            <p class="text-xs text-gray-600 mt-4 font-bangla" x-show="paymentMethod === 'bkash'">
                📱 bKash পেমেন্ট - অর্ডার নিশ্চিত করার পর bKash পেমেন্ট পেজে রিডাইরেক্ট করা হবে
            </p>
        </div>
        @endif

        @if($minQuantity > 0)
        <div x-show="quantity < {{ $minQuantity }}" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800 font-bangla">
                সর্বনিম্ন অর্ডার পরিমাণ: {{ $minQuantity }} টি
            </p>
        </div>
        @endif
        @if($maxQuantity > 0)
        <div x-show="quantity > {{ $maxQuantity }}" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800 font-bangla">
                সর্বোচ্চ অর্ডার পরিমাণ: {{ $maxQuantity }} টি
            </p>
        </div>
        @endif

        <button 
            type="submit"
            class="w-full btn-primary font-bangla text-base md:text-lg py-4 rounded-xl shadow-lg hover:shadow-xl break-words"
            style="background-color: var(--color-primary);"
            :disabled="!canSubmit()"
            :class="!canSubmit() ? 'opacity-50 cursor-not-allowed' : ''"
        >
            <span class="whitespace-normal">{{ $orderButtonText }} - ৳<span x-text="totalPrice.toLocaleString('bn-BD')"></span></span>
        </button>
    </form>
    </div>
</div>
