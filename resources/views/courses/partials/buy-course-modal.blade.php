{{-- Requires Alpine: buyOpen. Pass $course. --}}
<div
    x-show="buyOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    @keydown.escape.window="buyOpen = false"
>
    <div class="absolute inset-0 bg-black/50" @click="buyOpen = false"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 font-bangla">কোর্স কিনুন</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $course->title }}</p>
            </div>
            <button type="button" @click="buyOpen = false" class="text-gray-400 hover:text-gray-600 p-1" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="mb-4">
            @if($course->compare_at_price && $course->compare_at_price > $course->price)
                <span class="text-gray-400 line-through text-sm">৳{{ number_format($course->compare_at_price, 0) }}</span>
            @endif
            <span class="text-2xl font-bold text-gray-900 ml-1">৳{{ number_format($course->price, 0) }}</span>
        </div>

        <form method="POST" action="{{ route('courses.checkout', $course) }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 font-bangla">নাম</label>
                <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                @error('customer_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 font-bangla">মোবাইল</label>
                <input type="tel" name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" required placeholder="01XXXXXXXXX" class="w-full rounded-lg border-gray-300 text-sm">
                @error('customer_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-[#E2136E] hover:bg-[#c9105f] text-white py-3 rounded-lg font-semibold font-bangla transition">
                <img src="{{ route('assets.bkash.logo') }}" alt="" class="h-6 w-auto" onerror="this.style.display='none'">
                bKash দিয়ে কিনুন
            </button>
        </form>
        @guest
        <p class="text-xs text-gray-500 mt-3 text-center font-bangla">কেনার পর <a href="{{ route('login') }}" class="text-primary underline">লগইন</a> করে সব লেসন দেখুন</p>
        @endguest
    </div>
</div>
