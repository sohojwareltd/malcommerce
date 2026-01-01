@extends('layouts.admin')

@section('title', 'Edit Sponsor')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Edit Sponsor</h1>
</div>

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
    <form action="{{ route('admin.sponsors.update', $sponsor) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Profile Photo -->
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Profile Photo</label>
                <div class="flex items-center gap-6">
                    <div id="photo-preview-container">
                        @if($sponsor->photo)
                            <img id="photo-preview" src="{{ Storage::disk('public')->url($sponsor->photo) }}" alt="Profile Photo" class="w-24 h-24 rounded-full object-cover border-2 border-neutral-200">
                        @else
                            <div id="photo-preview-placeholder" class="w-24 h-24 rounded-full bg-neutral-200 flex items-center justify-center border-2 border-neutral-300">
                                <svg class="w-12 h-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="photo" accept="image/*" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light">
                        <p class="mt-1 text-xs text-neutral-500">JPG, PNG or GIF</p>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $sponsor->name) }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                @php
                    $displayPhone = old('phone', $sponsor->phone);
                    // Remove country code for display if present
                    if (!empty($displayPhone) && strpos($displayPhone, '880') === 0) {
                        $displayPhone = '0' . substr($displayPhone, 3);
                    }
                @endphp
                <input type="tel" name="phone" id="phone" value="{{ $displayPhone }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="01XXXXXXXXX">
                <p class="mt-1 text-xs text-neutral-500">Enter 11-digit phone number (e.g., 01712345678)</p>
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
                <textarea name="address" id="address" rows="4" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('address', $sponsor->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-light transition font-semibold">
                    Update Sponsor
                </button>
                <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="flex-1 bg-neutral-200 text-neutral-700 px-6 py-3 rounded-lg hover:bg-neutral-300 transition font-semibold text-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    
    // Format phone input - only allow numbers
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    }

    // Photo preview functionality
    const photoInput = document.getElementById('photo');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const photoPreview = document.getElementById('photo-preview');
    const photoPreviewPlaceholder = document.getElementById('photo-preview-placeholder');
    
    // Store original photo source if it exists
    const originalPhotoSrc = photoPreview ? photoPreview.src : null;
    
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file.');
                    photoInput.value = '';
                    return;
                }
                
                
                // Create FileReader to read the file
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Hide placeholder if it exists
                    if (photoPreviewPlaceholder) {
                        photoPreviewPlaceholder.style.display = 'none';
                    }
                    
                    // Create or update preview image
                    let previewImg = photoPreview;
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'photo-preview';
                        previewImg.className = 'w-24 h-24 rounded-full object-cover border-2 border-neutral-200';
                        previewImg.alt = 'Profile Photo Preview';
                        photoPreviewContainer.appendChild(previewImg);
                    }
                    
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            } else {
                // If no file selected, restore original photo or show placeholder
                if (originalPhotoSrc && photoPreview) {
                    photoPreview.src = originalPhotoSrc;
                    photoPreview.style.display = 'block';
                    if (photoPreviewPlaceholder) {
                        photoPreviewPlaceholder.style.display = 'none';
                    }
                } else {
                    // No original photo, show placeholder
                    if (photoPreview) {
                        photoPreview.style.display = 'none';
                    }
                    if (photoPreviewPlaceholder) {
                        photoPreviewPlaceholder.style.display = 'flex';
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

