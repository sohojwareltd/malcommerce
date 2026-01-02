@extends('layouts.admin')

@section('title', 'Create Sponsor')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Create Partner</h1>
</div>

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
    <form action="{{ route('admin.sponsors.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="01XXXXXXXXX">
                <p class="mt-1 text-xs text-neutral-500">Enter 11-digit phone number (e.g., 01712345678)</p>
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-light transition font-semibold">
                    Create Sponsor
                </button>
                <a href="{{ route('admin.sponsors.index') }}" class="flex-1 bg-neutral-200 text-neutral-700 px-6 py-3 rounded-lg hover:bg-neutral-300 transition font-semibold text-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    
    // Format phone input - only allow numbers
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>
@endsection

