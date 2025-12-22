@php
    /** @var \App\Models\Product $product */
@endphp

<div class="card" id="order">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 font-bangla">অর্ডার করুন</h2>

    <form action="{{ route('orders.store') }}" method="POST" x-data="{ quantity: 1, price: {{ $product->price }}, totalPrice: {{ $product->price }} }">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        {{-- Quantity Selector --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3 font-bangla">
                পরিমাণ
            </label>
            <div class="flex items-center gap-3">
                <button 
                    type="button"
                    @click="if(quantity > 1) { quantity--; totalPrice = quantity * price; }"
                    class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                >
                    -
                </button>
                <input 
                    type="number"
                    name="quantity"
                    x-model.number="quantity"
                    @input="totalPrice = quantity * price"
                    min="1"
                    max="{{ $product->stock_quantity ?? 999 }}"
                    class="w-20 text-center border border-gray-300 rounded-lg px-2 py-2 font-bold focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >
                <button 
                    type="button"
                    @click="if(quantity < {{ $product->stock_quantity ?? 999 }}) { quantity++; totalPrice = quantity * price; }"
                    class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                >
                    +
                </button>
                <span class="text-gray-600 font-bangla text-sm">(স্টকে: {{ $product->stock_quantity ?? '∞' }} টি)</span>
            </div>
        </div>

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
                    ইমেইল <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email"
                    name="customer_email"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="your@email.com"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                    পূর্ণ ঠিকানা <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="address"
                    rows="3"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla resize-none"
                    placeholder="বাড়ি নম্বর, রোড, এলাকা"
                ></textarea>
            </div>
        </div>

        {{-- BD Address Details --}}
        <div class="space-y-4 mb-6 border-t pt-6">
            <h3 class="font-semibold text-gray-900 font-bangla mb-4">ডেলিভারির ঠিকানা বিস্তারিত</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                        জেলা <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text"
                        name="district"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                        placeholder="ঢাকা"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                        উপজেলা <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text"
                        name="upazila"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                        placeholder="গুলশান"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                        শহর / গ্রাম <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text"
                        name="city_village"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                        placeholder="ধানমন্ডি"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                        পোস্ট কোড <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text"
                        name="post_code"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="1205"
                    >
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="bg-gray-50 rounded-xl p-6 mb-6 border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4 font-bangla">অর্ডার সারাংশ</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-bangla">পণ্যের মূল্য</span>
                    <span class="font-semibold text-gray-900">৳<span x-text="price.toLocaleString('bn-BD')"></span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-bangla">পরিমাণ</span>
                    <span class="font-semibold text-gray-900"><span x-text="quantity"></span> টি</span>
                </div>
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <span class="font-bangla">ডেলিভারি চার্জ</span>
                    <span class="font-bangla text-green-600">ফ্রি</span>
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

        <button 
            type="submit"
            class="w-full btn-primary font-bangla text-lg py-4 rounded-xl shadow-lg hover:shadow-xl"
            style="background-color: var(--color-primary);"
        >
            <span>অর্ডার নিশ্চিত করুন - ৳<span x-text="totalPrice.toLocaleString('bn-BD')"></span></span>
        </button>
    </form>
</div>
