@extends('layouts.app')

@section('title', 'Take Exam')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">{{ $attempt->exam->title }}</h1>
            <p class="text-sm text-gray-500">{{ $questions->count() }} questions</p>
        </div>
        @if($attempt->expires_at)
        <div class="rounded-lg bg-amber-50 px-4 py-2 text-sm text-amber-800" x-data="{
            expires: new Date('{{ $attempt->expires_at->toIso8601String() }}').getTime(),
            remaining: '',
            tick() {
                const diff = this.expires - Date.now();
                if (diff <= 0) { this.remaining = 'Time up'; return; }
                const m = Math.floor(diff / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                this.remaining = m + 'm ' + s + 's';
            }
        }" x-init="tick(); setInterval(() => tick(), 1000)">
            Time left: <span x-text="remaining"></span>
        </div>
        @endif
    </div>

    <form method="POST" action="{{ route('exams.submit', $attempt) }}" class="space-y-6">
        @csrf
        @foreach($questions as $index => $question)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <p class="font-semibold text-gray-900">{{ $index + 1 }}. {{ $question->question_text }}</p>
                @if($question->image_url)
                <img src="{{ $question->image_url }}" alt="" class="mt-3 max-h-64 w-auto rounded-lg border object-contain">
                @endif
            </div>
            <div class="space-y-2">
                @foreach($question->options as $option)
                <label class="flex items-start gap-3 rounded-lg border border-gray-100 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="mt-1">
                    <div class="min-w-0">
                        @if($option->option_text)
                        <span>{{ $option->option_text }}</span>
                        @endif
                        @if($option->image_url)
                        <img src="{{ $option->image_url }}" alt="" class="mt-2 max-h-32 w-auto rounded border object-contain">
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach

        <button type="submit" class="w-full rounded-lg bg-primary text-white py-3 font-semibold font-bangla" onclick="return confirm('Submit exam?')">জমা দিন</button>
    </form>
</div>
@endsection
