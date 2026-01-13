@extends('layouts.sponsor')

@section('title', 'Edit Profile')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Edit Profile</h1>
        <p class="text-neutral-600 mt-1">Update your personal information</p>
    </div>
    <a href="{{ route('sponsor.dashboard') }}" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
        ← Back to Dashboard
    </a>
</div>

@if(empty($user->password) || session('password_required'))
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-yellow-800 mb-1">Password Required</h3>
                <p class="text-sm text-yellow-700">Please set a password to continue using the partner dashboard. You can set it in the "Change Password" section below.</p>
            </div>
        </div>
    </div>
@endif

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mb-6">
    <h2 class="text-xl font-bold mb-4">Profile Information</h2>
    <form action="{{ route('sponsor.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Profile Photo -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-neutral-700 mb-2">Profile Photo</label>
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <div id="photo-preview-container" class="flex-shrink-0">
                    @if($user->photo)
                        <img id="photo-preview" src="{{ Storage::disk('public')->url($user->photo) }}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover border-4 border-primary shadow-lg transition-all duration-300">
                    @else
                        <div id="photo-preview-placeholder" class="w-32 h-32 rounded-full bg-neutral-200 flex items-center justify-center border-4 border-neutral-300 shadow-lg transition-all duration-300">
                            <svg class="w-16 h-16 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <div id="preview-loading" class="hidden mt-2 text-center">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                        <p class="text-xs text-neutral-500 mt-1">Loading preview...</p>
                    </div>
                </div>
                <div class="flex-1 w-full">
                    <input type="file" name="photo" id="photo" accept="image/*" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light transition">
                    <p class="mt-1 text-xs text-neutral-500">JPG, PNG or GIF</p>
                    <p id="file-info" class="mt-1 text-xs text-green-600 hidden"></p>
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Phone -->
        <div class="mb-6">
            <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('phone') border-red-500 @enderror">
            <p class="mt-1 text-xs text-neutral-500">Format: 01795560431 or 8801795560431</p>
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Address -->
        <div class="mb-6">
            <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
            <textarea name="address" id="address" rows="4"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('address') border-red-500 @enderror">{{ old('address', $user->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Submit Button -->
        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
                Update Profile
            </button>
            <a href="{{ route('sponsor.dashboard') }}" class="px-6 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Password Section -->
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl" id="password-section">
    <h2 class="text-xl font-bold mb-4">Change Password</h2>
    <form action="{{ route('sponsor.profile.update-password') }}" method="POST">
        @csrf
        @method('PUT')
        
        @if($user->password)
        <div class="mb-4">
            <label for="current_password" class="block text-sm font-medium text-neutral-700 mb-2">Current Password <span class="text-red-500">*</span></label>
            <input type="password" name="current_password" id="current_password" required 
                   class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        @else
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">No Password Set</p>
                    <p class="text-sm text-blue-700 mt-1">You don't have a password set yet. You can set one now to enable password-based login.</p>
                </div>
            </div>
        </div>
        @endif
        
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" id="password" required 
                   class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            <p class="mt-1 text-xs text-neutral-500">Minimum 6 characters</p>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" required 
                   class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
        
        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
                {{ $user->password ? 'Update Password' : 'Set Password' }}
            </button>
            <a href="{{ route('sponsor.dashboard') }}" class="px-6 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition font-semibold">
                Cancel
            </a>
        </div>
    </form>
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
                    previewImg.className = 'w-32 h-32 rounded-full object-cover border-4 border-primary shadow-lg transition-all duration-300';
                    previewImg.alt = 'Profile Photo Preview';
                    photoPreviewContainer.insertBefore(previewImg, previewLoading);
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

