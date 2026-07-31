@extends('layouts.app')

@section('title', $exam->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10" x-data="{ buyOpen: {{ ($errors->any() && !$enrollment && $exam->requiresPayment()) ? 'true' : 'false' }}, codeOpen: {{ ($errors->any() && !$enrollment && $exam->requiresCode()) ? 'true' : 'false' }} }">
    @if(session('success'))<div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>@endif
    @if(session('info'))<div class="mb-4 rounded-lg bg-blue-50 text-blue-800 px-4 py-3 text-sm">{{ session('info') }}</div>@endif

    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $exam->title }}</h1>
    @if($exam->description)<div class="prose prose-sm max-w-none text-gray-700 mb-6">{!! nl2br(e($exam->description)) !!}</div>@endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4 text-sm">
        <div class="grid grid-cols-2 gap-4">
            <div><span class="text-gray-500">Questions</span><div class="font-semibold">{{ $exam->questions_count }}</div></div>
            <div><span class="text-gray-500">Pass mark</span><div class="font-semibold">{{ $exam->pass_mark }}%</div></div>
            <div><span class="text-gray-500">Exam attempts allowed</span><div class="font-semibold">{{ $exam->max_exam_attempts }}</div></div>
            @if($exam->duration_minutes)<div><span class="text-gray-500">Duration</span><div class="font-semibold">{{ $exam->duration_minutes }} min</div></div>@endif
        </div>

        @auth
            @if($passed)
                <div class="rounded-lg bg-green-50 text-green-800 px-4 py-3 font-bangla">আপনি ইতিমধ্যে এই পরীক্ষায় পাস করেছেন।</div>
                @if($certificate)
                <a href="{{ route('certificates.show', $certificate) }}" class="inline-block rounded-lg bg-primary text-white px-5 py-2.5 font-semibold font-bangla">সার্টিফিকেট দেখুন</a>
                @endif
            @elseif($enrollment)
                <p class="text-gray-700 font-bangla">অবশিষ্ট চেষ্টা: <strong>{{ $remainingAttempts }}</strong></p>
                @if($remainingAttempts > 0)
                <form method="POST" action="{{ route('exams.start', $exam) }}">@csrf<button class="rounded-lg bg-primary text-white px-6 py-3 font-semibold font-bangla">পরীক্ষা শুরু করুন</button></form>
                @else
                <p class="text-red-600 font-bangla">আপনার সব চেষ্টা শেষ।</p>
                @endif
            @else
                @if($exam->allowsFreeStart())
                <form method="POST" action="{{ route('exams.start', $exam) }}">@csrf<button class="rounded-lg bg-primary text-white px-6 py-3 font-semibold font-bangla">পরীক্ষা শুরু করুন</button></form>
                @endif

                @if($exam->requiresPayment())
                <button type="button" @click="buyOpen = true" class="rounded-lg bg-primary text-white px-6 py-3 font-semibold font-bangla">৳{{ number_format($exam->price, 0) }} — কিনুন</button>
                @endif

                @if($exam->requiresCode())
                    @if($codeBanned)
                    <p class="text-red-600 font-bangla">আপনি access code দেওয়া থেকে নিষিদ্ধ। অ্যাডমিনের সাথে যোগাযোগ করুন।</p>
                    @else
                    <button type="button" @click="codeOpen = true" class="rounded-lg border border-primary text-primary px-6 py-3 font-semibold font-bangla">Access code দিন</button>
                    @if($remainingCodeAttempts !== null)
                    <p class="text-xs text-gray-500 font-bangla">ভুল code চেষ্টা বাকি: {{ $remainingCodeAttempts }}</p>
                    @endif
                    @endif
                @endif
            @endif
        @else
            <p class="font-bangla text-gray-700 mb-3">পরীক্ষায় অংশ নিতে লগইন করুন।</p>
            <a href="{{ route('login') }}" class="inline-block rounded-lg bg-primary text-white px-6 py-3 font-semibold font-bangla">লগইন</a>
        @endauth
    </div>

    @if($exam->requiresPayment())
    <div x-show="buyOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="buyOpen = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-4 font-bangla">পরীক্ষা কিনুন</h3>
            <form method="POST" action="{{ route('exams.checkout', $exam) }}" class="space-y-4">
                @csrf
                <div><label class="block text-sm mb-1 font-bangla">নাম</label><input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required class="w-full rounded-lg border px-3 py-2"></div>
                <div><label class="block text-sm mb-1 font-bangla">মোবাইল</label><input type="text" name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" required class="w-full rounded-lg border px-3 py-2"></div>
                @error('customer_phone')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <button class="w-full rounded-lg bg-primary text-white py-3 font-semibold font-bangla">bKash দিয়ে কিনুন — ৳{{ number_format($exam->price, 0) }}</button>
            </form>
        </div>
    </div>
    @endif

    @if($exam->requiresCode())
    <div x-show="codeOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.outside="codeOpen = false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-4 font-bangla">৬-অঙ্কের access code</h3>
            <form method="POST" action="{{ route('exams.redeem-code', $exam) }}" class="space-y-4">
                @csrf
                <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required class="w-full rounded-lg border px-3 py-3 text-center text-2xl tracking-widest font-mono" placeholder="000000">
                <button class="w-full rounded-lg bg-primary text-white py-3 font-semibold font-bangla">যাচাই করুন</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
