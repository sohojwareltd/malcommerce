@extends('layouts.admin')

@section('title', 'Add Workshop/Seminar')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Add Workshop/Seminar</h1>
            <p class="text-neutral-600 mt-1 text-sm">Create a new workshop or seminar</p>
        </div>
        <a href="{{ route('admin.workshop-seminars.index') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm">← Back</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.workshop-seminars.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="border-b border-neutral-200 mb-6">
            <div class="inline-flex rounded-lg border border-neutral-200 overflow-hidden">
                <button type="button" data-tab-target="details" class="tab-button px-4 py-2 text-sm font-semibold text-primary bg-primary/10">Details</button>
                <button type="button" data-tab-target="sms" class="tab-button px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100">SMS Settings</button>
            </div>
        </div>

        <div id="tab-details" class="tab-panel space-y-6">
            <div>
            <label for="title" class="block text-sm font-medium text-neutral-700 mb-2">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('title') border-red-500 @enderror">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-neutral-700 mb-2">Slug</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="Auto-generated if empty"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('slug') border-red-500 @enderror">
            @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="thumbnail" class="block text-sm font-medium text-neutral-700 mb-2">Thumbnail</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('thumbnail') border-red-500 @enderror">
            <p class="mt-1 text-xs text-neutral-500">jpeg, png, jpg, gif, webp — max 10MB</p>
            @error('thumbnail')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-neutral-700 mb-2">Description</label>
            <textarea name="description" id="description" rows="5"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="venue" class="block text-sm font-medium text-neutral-700 mb-2">Venue</label>
                <input type="text" name="venue" id="venue" value="{{ old('venue') }}"
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('venue') border-red-500 @enderror">
                @error('venue')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="event_date" class="block text-sm font-medium text-neutral-700 mb-2">Event Date</label>
                <input type="date" name="event_date" id="event_date" value="{{ old('event_date') }}"
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('event_date') border-red-500 @enderror">
                @error('event_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="event_time" class="block text-sm font-medium text-neutral-700 mb-2">Event Time</label>
                <input type="time" name="event_time" id="event_time" value="{{ old('event_time') }}"
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('event_time') border-red-500 @enderror">
                @error('event_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="max_participants" class="block text-sm font-medium text-neutral-700 mb-2">Max Participants</label>
                <input type="number" name="max_participants" id="max_participants" value="{{ old('max_participants') }}" min="1"
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('max_participants') border-red-500 @enderror" placeholder="Leave empty for unlimited">
                @error('max_participants')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded">
                <span class="text-sm font-medium text-neutral-700">Active (accepting enrollments)</span>
            </label>
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded">
                <span class="text-sm font-medium text-neutral-700">Featured (show on homepage)</span>
            </label>
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-medium text-neutral-700 mb-2">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                class="w-full max-w-[120px] px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('sort_order') border-red-500 @enderror">
            <p class="mt-1 text-xs text-neutral-500">Lower numbers appear first</p>
            @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        </div><!-- End tab-details -->

        @php
            $enrollmentStatuses = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'];
            $workshopSmsTemplates = old('sms_templates', []);
        @endphp
        <div id="tab-sms" class="tab-panel hidden">
            <h2 class="text-lg font-semibold mb-2">Enrollment Status Messages (SMS)</h2>
            <p class="text-sm text-neutral-600 mb-4">Set custom SMS messages for each enrollment status. Sent when you change an enrollment's status. Leave blank to use default.</p>
            <div class="grid grid-cols-1 gap-4">
                @foreach($enrollmentStatuses as $statusKey => $statusLabel)
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-neutral-800">{{ $statusLabel }} Message <span class="text-neutral-400 font-normal text-xs uppercase">{{ $statusKey }}</span></label>
                    <textarea name="sms_templates[{{ $statusKey }}]" rows="3" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="e.g., Dear {name}, your enrollment for {workshop_title} is {{ strtolower($statusLabel) }}. Venue: {venue}, Date: {event_date}.">{{ $workshopSmsTemplates[$statusKey] ?? '' }}</textarea>
                    <p class="text-xs text-neutral-500">Placeholders: {name}, {workshop_title}, {status}, {phone}, {venue}, {event_date}, {event_time}</p>
                </div>
                @endforeach
            </div>
        </div><!-- End tab-sms -->

        <div class="flex gap-3 pt-4 border-t border-neutral-200">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold">Create</button>
            <a href="{{ route('admin.workshop-seminars.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('.tab-button');
    var tabPanels = document.querySelectorAll('.tab-panel');
    function activateTab(target) {
        tabPanels.forEach(function(panel) {
            panel.classList.toggle('hidden', panel.id !== 'tab-' + target);
        });
        tabButtons.forEach(function(button) {
            var isActive = button.dataset.tabTarget === target;
            button.classList.toggle('bg-primary/10', isActive);
            button.classList.toggle('text-primary', isActive);
            button.classList.toggle('text-neutral-700', !isActive);
        });
    }
    tabButtons.forEach(function(button) {
        button.addEventListener('click', function() { activateTab(button.dataset.tabTarget); });
    });
    activateTab('details');
});
</script>
@endpush
@endsection
