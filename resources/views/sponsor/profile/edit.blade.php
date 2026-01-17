@extends('layouts.sponsor')

@section('title', 'Edit Profile')

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
    <!-- Header -->
    <div class="app-card mx-4 mt-4 mb-4 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-base sm:text-lg md:text-xl font-bold" style="color: var(--color-dark);">Edit Profile</h1>
                <p class="text-xs sm:text-sm mt-1" style="color: var(--color-medium);">Update your personal information</p>
            </div>
            <a href="{{ route('sponsor.dashboard') }}" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold whitespace-nowrap" style="background: var(--color-accent); color: var(--color-dark);">
                ← Back
            </a>
        </div>
    </div>

    @if(empty($user->password) || session('password_required'))
    <div class="app-card mx-4 mb-4 p-3 sm:p-4">
        <div class="p-2 sm:p-3 md:p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
            <div class="flex items-start">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-xs sm:text-sm font-semibold text-yellow-800 mb-0.5 sm:mb-1">Password Required</h3>
                    <p class="text-xs sm:text-sm text-yellow-700">Please set a password to continue using the partner dashboard. You can set it in the "Change Password" section below.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Personal Information Table -->
    <div class="app-card mx-4 mb-4 overflow-hidden max-w-2xl">
        <div class="p-3 sm:p-4 md:p-6">
            <h2 class="text-base sm:text-lg md:text-xl font-bold mb-3 sm:mb-4" style="color: var(--color-dark);">Personal Information</h2>
            <form action="{{ route('sponsor.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <tbody class="divide-y" style="border-color: var(--color-accent);">
                            <!-- Profile Photo -->
                            <tr>
                                <td class="px-2 sm:px-4 py-3 sm:py-4 align-top">
                                    <label class="block text-xs sm:text-sm font-medium mb-2 sm:mb-3" style="color: var(--color-dark);">Profile Photo</label>
                                </td>
                                <td class="px-2 sm:px-4 py-3 sm:py-4">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="relative group">
                                            <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                                            <label for="photo" class="cursor-pointer">
                                                <div id="photo-preview-container" class="relative">
                                                    @if($user->photo)
                                                        <img id="photo-preview" src="{{ Storage::disk('public')->url($user->photo) }}" alt="Profile Photo" class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full object-cover border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90" style="border-color: var(--color-medium);">
                                                    @else
                                                        <div id="photo-preview-placeholder" class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full bg-gradient-to-br flex items-center justify-center border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90" style="background: linear-gradient(135deg, var(--color-light)/20 0%, var(--color-accent)/30 100%); border-color: var(--color-light)/30;">
                                                            <svg class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14" style="color: var(--color-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div id="photo-overlay" class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </label>
                                            <div id="preview-loading" class="hidden mt-2 text-center">
                                                <div class="inline-block animate-spin rounded-full h-4 w-4 sm:h-5 sm:w-5 border-b-2" style="border-color: var(--color-medium);"></div>
                                                <p class="text-xs mt-1" style="color: var(--color-medium);">Loading...</p>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs sm:text-sm" style="color: var(--color-medium);">Click the circle to upload</p>
                                            <p class="text-xs mt-0.5" style="color: var(--color-light);">JPG, PNG or GIF</p>
                                            <p id="file-info" class="mt-1 text-xs text-green-600 hidden"></p>
                                            @error('photo')
                                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Name -->
                            <tr>
                                <td class="px-2 sm:px-4 py-3 sm:py-4 align-top">
                                    <label for="name" class="block text-xs sm:text-sm font-medium" style="color: var(--color-dark);">Name</label>
                                </td>
                                <td class="px-2 sm:px-4 py-3 sm:py-4">
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none @error('name') border-red-500 @enderror"
                                        style="border-color: var(--color-accent);">
                                    @error('name')
                                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            
                            <!-- Phone -->
                            <tr>
                                <td class="px-2 sm:px-4 py-3 sm:py-4 align-top">
                                    <label for="phone" class="block text-xs sm:text-sm font-medium" style="color: var(--color-dark);">Phone</label>
                                </td>
                                <td class="px-2 sm:px-4 py-3 sm:py-4">
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required
                                        class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none @error('phone') border-red-500 @enderror"
                                        style="border-color: var(--color-accent);">
                                    <p class="mt-1 text-xs" style="color: var(--color-medium);">Format: 01795560431 or 8801795560431</p>
                                    @error('phone')
                                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            
                            <!-- Address -->
                            <tr>
                                <td class="px-2 sm:px-4 py-3 sm:py-4 align-top">
                                    <label for="address" class="block text-xs sm:text-sm font-medium" style="color: var(--color-dark);">Address</label>
                                </td>
                                <td class="px-2 sm:px-4 py-3 sm:py-4">
                                    <textarea name="address" id="address" rows="2"
                                        class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none resize-none @error('address') border-red-500 @enderror"
                                        style="border-color: var(--color-accent);">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mt-4 sm:mt-6 pt-4 border-t" style="border-color: var(--color-accent);">
                    <button type="submit" class="px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-white text-xs sm:text-sm font-semibold transition" style="background: var(--color-medium);">
                        Update Profile
                    </button>
                    <a href="{{ route('sponsor.dashboard') }}" class="px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold text-center transition" style="background: var(--color-accent); color: var(--color-dark);">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Table -->
    <div class="app-card mx-4 mb-4 overflow-hidden max-w-2xl" id="password-section">
        <div class="p-3 sm:p-4 md:p-6">
            <h2 class="text-base sm:text-lg md:text-xl font-bold mb-3 sm:mb-4" style="color: var(--color-dark);">Change Password</h2>
            <form action="{{ route('sponsor.profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                
                @if(!$user->password)
                <div class="mb-3 sm:mb-4 p-2 sm:p-3 md:p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-400 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-blue-800">No Password Set</p>
                            <p class="text-xs sm:text-sm text-blue-700 mt-0.5 sm:mt-1">You don't have a password set yet. You can set one now to enable password-based login.</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <tbody class="divide-y" style="border-color: var(--color-accent);">
                            @if($user->password)
                            <!-- Current Password -->
                            <tr>
                                <td class="px-2 sm:px-4 py-3 sm:py-4 align-top w-1/3">
                                    <label for="current_password" class="block text-xs sm:text-sm font-medium" style="color: var(--color-dark);">Current Password <span class="text-red-500">*</span></label>
                                </td>
                                <td class="px-2 sm:px-4 py-3 sm:py-4">
                                    <div class="relative">
                                        <input type="password" name="current_password" id="current_password" required 
                                               class="w-full px-3 sm:px-4 py-1.5 sm:py-2 pr-10 sm:pr-12 text-sm sm:text-base border-2 rounded-xl focus:outline-none"
                                               style="border-color: var(--color-accent);">
                                        <button type="button" id="toggle-current-password" class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-700 focus:outline-none" aria-label="Toggle password visibility">
                                            <svg id="eye-icon-current" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <svg id="eye-off-icon-current" class="w-4 h-4 sm:w-5 sm:h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l18 18"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            @endif
                            
                            <!-- New Password -->
                            <tr>
                                <td class="px-2 sm:px-4 py-3 sm:py-4 align-top w-1/3">
                                    <label for="password" class="block text-xs sm:text-sm font-medium" style="color: var(--color-dark);">New Password <span class="text-red-500">*</span></label>
                                </td>
                                <td class="px-2 sm:px-4 py-3 sm:py-4">
                                    <div class="relative">
                                        <input type="password" name="password" id="password" required 
                                               class="w-full px-3 sm:px-4 py-1.5 sm:py-2 pr-10 sm:pr-12 text-sm sm:text-base border-2 rounded-xl focus:outline-none"
                                               style="border-color: var(--color-accent);">
                                        <button type="button" id="toggle-password" class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-700 focus:outline-none" aria-label="Toggle password visibility">
                                            <svg id="eye-icon" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <svg id="eye-off-icon" class="w-4 h-4 sm:w-5 sm:h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l18 18"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs" style="color: var(--color-medium);">Minimum 6 characters</p>
                                    @error('password')
                                        <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            
                            <!-- Confirm Password -->
                            <tr>
                                <td class="px-2 sm:px-4 py-3 sm:py-4 align-top w-1/3">
                                    <label for="password_confirmation" class="block text-xs sm:text-sm font-medium" style="color: var(--color-dark);">Confirm Password <span class="text-red-500">*</span></label>
                                </td>
                                <td class="px-2 sm:px-4 py-3 sm:py-4">
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                                               class="w-full px-3 sm:px-4 py-1.5 sm:py-2 pr-10 sm:pr-12 text-sm sm:text-base border-2 rounded-xl focus:outline-none"
                                               style="border-color: var(--color-accent);">
                                        <button type="button" id="toggle-password-confirmation" class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-700 focus:outline-none" aria-label="Toggle password visibility">
                                            <svg id="eye-icon-confirmation" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <svg id="eye-off-icon-confirmation" class="w-4 h-4 sm:w-5 sm:h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l18 18"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mt-4 sm:mt-6 pt-4 border-t" style="border-color: var(--color-accent);">
                    <button type="submit" class="px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-white text-xs sm:text-sm font-semibold transition" style="background: var(--color-medium);">
                        {{ $user->password ? 'Update Password' : 'Set Password' }}
                    </button>
                    <a href="{{ route('sponsor.dashboard') }}" class="px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold text-center transition" style="background: var(--color-accent); color: var(--color-dark);">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@if(empty($user->password) || session('password_required'))
<script>
    // Scroll to password section if password is required
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#password' || {{ empty($user->password) ? 'true' : 'false' }}) {
            document.getElementById('password-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
</script>
@endif

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
                    previewImg.className = 'w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full object-cover border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90';
                    previewImg.style.borderColor = 'var(--color-medium)';
                    previewImg.alt = 'Profile Photo Preview';
                    // Insert before the overlay
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

    // Password visibility toggles
    const currentPasswordInput = document.getElementById('current_password');
    const toggleCurrentPasswordBtn = document.getElementById('toggle-current-password');
    const eyeIconCurrent = document.getElementById('eye-icon-current');
    const eyeOffIconCurrent = document.getElementById('eye-off-icon-current');

    if (toggleCurrentPasswordBtn && currentPasswordInput) {
        toggleCurrentPasswordBtn.addEventListener('click', function() {
            const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            currentPasswordInput.setAttribute('type', type);
            
            if (type === 'text') {
                eyeIconCurrent.classList.add('hidden');
                eyeOffIconCurrent.classList.remove('hidden');
            } else {
                eyeIconCurrent.classList.remove('hidden');
                eyeOffIconCurrent.classList.add('hidden');
            }
        });
    }

    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('toggle-password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeOffIcon = document.getElementById('eye-off-icon');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        });
    }

    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const togglePasswordConfirmationBtn = document.getElementById('toggle-password-confirmation');
    const eyeIconConfirmation = document.getElementById('eye-icon-confirmation');
    const eyeOffIconConfirmation = document.getElementById('eye-off-icon-confirmation');

    if (togglePasswordConfirmationBtn && passwordConfirmationInput) {
        togglePasswordConfirmationBtn.addEventListener('click', function() {
            const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmationInput.setAttribute('type', type);
            
            if (type === 'text') {
                eyeIconConfirmation.classList.add('hidden');
                eyeOffIconConfirmation.classList.remove('hidden');
            } else {
                eyeIconConfirmation.classList.remove('hidden');
                eyeOffIconConfirmation.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection

