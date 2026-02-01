@extends('layouts.sponsor')

@section('title', 'Edit Referral User')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
    :root {
        --color-dark: #0F2854;
        --color-medium: #1C4D8D;
        --color-light: #4988C4;
        --color-accent: #BDE8F5;
    }
    
    .app-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 40, 84, 0.08);
    }
</style>

<div class="min-h-screen pb-6">
   

    <div class="app-card mx-4 mb-4 p-3 sm:p-4 md:p-6 max-w-2xl">
    <form action="{{ route('sponsor.users.update', $referral) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Profile Photo -->
        <div class="mb-3 sm:mb-4 md:mb-6 rounded-2xl p-4 text-white" style="background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-medium) 100%);">
            <label class="block text-xs sm:text-sm font-medium mb-2 sm:mb-3" style="color: var(--color-white);">Profile Photo</label>
            <div class="flex flex-col items-center gap-3">
                <div class="relative group">
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                    <label for="photo" class="cursor-pointer">
                        <div id="photo-preview-container" class="relative">
                            @if($referral->photo)
                                <img id="photo-preview" src="{{ Storage::disk('public')->url($referral->photo) }}" alt="Profile Photo" class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full object-cover border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90" style="border-color: var(--color-white);">
                            @else
                                <div id="photo-preview-placeholder" class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full bg-gradient-to-br flex items-center justify-center border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90" style="background: linear-gradient(135deg, var(--color-light)/20 0%, var(--color-accent)/30 100%); border-color: var(--color-light)/30;">
                                    <svg class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16" style="color: var(--color-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div id="photo-overlay" class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </label>
                    <div id="preview-loading" class="hidden mt-2 text-center">
                        <div class="inline-block animate-spin rounded-full h-5 w-5 sm:h-6 sm:w-6 border-b-2" style="border-color: var(--color-medium);"></div>
                        <p class="text-xs mt-1" style="color: var(--color-medium);">Loading...</p>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-xs sm:text-sm" style="color: var(--color-white);">Click the circle to upload photo</p>
                    <p class="text-xs mt-0.5" style="color: var(--color-white);">JPG, PNG or GIF</p>
                    <p id="file-info" class="mt-1 text-xs text-green-600 hidden"></p>
                    @error('photo')
                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Name -->
        <div class="mb-3 sm:mb-4 md:mb-6">
            <label for="name" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $referral->name) }}" required
                class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none @error('name') border-red-500 @enderror"
                style="border-color: var(--color-accent);">
            @error('name')
                <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone (Read-only) -->
        <div class="mb-3 sm:mb-4 md:mb-6">
            <label for="phone" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Phone</label>
            <input type="text" value="{{ $referral->phone }}" readonly
                class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl bg-gray-50 cursor-not-allowed"
                style="border-color: var(--color-accent); color: var(--color-medium);">
            <p class="mt-0.5 sm:mt-1 text-xs" style="color: var(--color-medium);">Phone number cannot be changed</p>
        </div>
        
        <!-- Address -->
        <div class="mb-3 sm:mb-4 md:mb-6">
            <label for="address" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Address</label>
            <textarea name="address" id="address" rows="2"
                class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none resize-none @error('address') border-red-500 @enderror"
                style="border-color: var(--color-accent);">{{ old('address', $referral->address) }}</textarea>
            @error('address')
                <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Comment -->
        <div class="mb-3 sm:mb-4 md:mb-6">
            <label for="comment" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Comment</label>
            <textarea name="comment" id="comment" rows="2"
                class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none resize-none @error('comment') border-red-500 @enderror"
                style="border-color: var(--color-accent);"
                placeholder="Add any comments or notes about this user">{{ old('comment', $referral->comment ?? '') }}</textarea>
            @error('comment')
                <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Affiliate Code (Read-only) -->
        <div class="mb-3 sm:mb-4 md:mb-6">
            <label for="affiliate_code" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Affiliate Code</label>
            <input type="text" value="{{ $referral->affiliate_code }}" readonly
                class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl bg-gray-50 font-mono cursor-not-allowed"
                style="border-color: var(--color-accent); color: var(--color-medium);">
            <p class="mt-0.5 sm:mt-1 text-xs" style="color: var(--color-medium);">Affiliate code cannot be changed</p>
        </div>
        
        <!-- Submit Button -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 pt-1">
            <button type="submit" class="px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-white text-xs sm:text-sm font-semibold transition" style="background: var(--color-medium);">
                Update User
            </button>
            <a href="{{ route('sponsor.dashboard') }}" class="px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold text-center transition" style="background: var(--color-accent); color: var(--color-dark);">
                Cancel
            </a>
        </div>
    </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const photoPreview = document.getElementById('photo-preview');
    const photoPreviewPlaceholder = document.getElementById('photo-preview-placeholder');
    const previewLoading = document.getElementById('preview-loading');
    const fileInfo = document.getElementById('file-info');
    
    // Store original photo source if it exists
    const originalPhotoSrc = photoPreview ? photoPreview.src : null;
    
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file.');
                photoInput.value = '';
                if (fileInfo) fileInfo.classList.add('hidden');
                return;
            }
            
            
            // Show loading indicator
            if (previewLoading) {
                previewLoading.classList.remove('hidden');
            }
            
            // Show file info
            if (fileInfo) {
                const fileSize = (file.size / 1024).toFixed(2);
                fileInfo.textContent = `Selected: ${file.name} (${fileSize} KB)`;
                fileInfo.classList.remove('hidden');
            }
            
            // Create FileReader to read the file
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Hide loading indicator
                if (previewLoading) {
                    previewLoading.classList.add('hidden');
                }
                
                // Hide placeholder if it exists
                if (photoPreviewPlaceholder) {
                    photoPreviewPlaceholder.style.display = 'none';
                }
                
                // Create or update preview image
                let previewImg = photoPreview;
                if (!previewImg) {
                    previewImg = document.createElement('img');
                    previewImg.id = 'photo-preview';
                    previewImg.className = 'w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full object-cover border-[3px] sm:border-4 shadow-lg transition-all duration-300';
                    previewImg.style.borderColor = 'var(--color-medium)';
                    previewImg.alt = 'Profile Photo Preview';
                    const overlay = photoPreviewContainer.querySelector('#photo-overlay');
                    if (overlay) {
                        photoPreviewContainer.insertBefore(previewImg, overlay);
                    } else {
                        photoPreviewContainer.appendChild(previewImg);
                    }
                }
                
                // Add fade-in effect
                previewImg.style.opacity = '0';
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                
                // Fade in animation
                setTimeout(() => {
                    previewImg.style.transition = 'opacity 0.3s ease-in-out';
                    previewImg.style.opacity = '1';
                }, 10);
            };
            
            reader.onerror = function() {
                if (previewLoading) {
                    previewLoading.classList.add('hidden');
                }
                alert('Error reading file. Please try again.');
                photoInput.value = '';
                if (fileInfo) fileInfo.classList.add('hidden');
            };
            
            reader.readAsDataURL(file);
        } else {
            // If no file selected, restore original photo or show placeholder
            if (fileInfo) fileInfo.classList.add('hidden');
            if (previewLoading) previewLoading.classList.add('hidden');
            
            if (originalPhotoSrc && photoPreview) {
                photoPreview.src = originalPhotoSrc;
                photoPreview.style.display = 'block';
                photoPreview.style.opacity = '1';
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
});
</script>
@endpush
@endsection


