@extends('layouts.admin')

@section('title', 'Confirm Send SMS')

@section('content')
<div class="mb-4 sm:mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold">Confirm Send SMS</h1>
    <p class="text-neutral-600 mt-1 text-sm">Review the recipient list below, then confirm to send.</p>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 max-w-3xl space-y-6">
    <p class="text-lg font-semibold">You are about to send to <strong>{{ $recipients->count() }}</strong> recipient(s).</p>

    <div>
        <h2 class="text-sm font-semibold text-neutral-700 mb-2">Recipient list (full)</h2>
        <div class="border border-neutral-200 rounded-lg p-4 max-h-80 overflow-y-auto bg-neutral-50">
            <ul class="space-y-1 text-sm font-mono">
                @foreach($recipients as $r)
                    <li>{{ $r['phone'] }}{{ $r['name'] ? ' — ' . $r['name'] : '' }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div>
        <h2 class="text-sm font-semibold text-neutral-700 mb-2">Message</h2>
        <div class="border border-neutral-200 rounded-lg p-4 bg-neutral-50 whitespace-pre-wrap text-sm">{{ $message }}</div>
        <p class="mt-1 text-xs text-neutral-500">Placeholder <code>{name}</code> will be replaced with each recipient's name (or "Customer").</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.sms-send.send') }}">
        @csrf
        <input type="hidden" name="message" value="{{ $message }}">
        <input type="hidden" name="source" value="{{ $source }}">
        @foreach($requestParams as $key => $value)
            @if($value !== null && $value !== '')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <div class="flex gap-3">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold">Confirm & Send</button>
            <a href="{{ route('admin.settings.sms-send') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">Back to form</a>
        </div>
    </form>
</div>
@endsection
