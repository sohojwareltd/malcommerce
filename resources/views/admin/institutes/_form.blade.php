@php
    $inputClass = 'w-full rounded-lg border border-neutral-300 px-4 py-2.5 text-sm';
    $labelClass = 'mb-1.5 block text-sm font-medium text-neutral-700';
@endphp

<div class="max-w-lg rounded-xl border bg-white p-6 shadow-sm">
    <form method="POST" action="{{ $institute ? route('admin.institutes.update', $institute) : route('admin.institutes.store') }}" class="space-y-4">
        @csrf
        @if($institute) @method('PUT') @endif

        <div>
            <label class="{{ $labelClass }}">Name *</label>
            <input type="text" name="name" value="{{ old('name', $institute?->name) }}" required class="{{ $inputClass }}" placeholder="Bangladesh Medical Education Institute">
        </div>

        <div class="flex gap-3">
            <button class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Save institute</button>
            <a href="{{ route('admin.institutes.index') }}" class="rounded-lg border px-5 py-2.5 text-sm">Back</a>
        </div>
    </form>
</div>
