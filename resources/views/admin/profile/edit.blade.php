@extends('layouts.admin')

@section('title', 'Profile Settings')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-primary-light px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Profile Settings</h1>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="border-b border-neutral-200 bg-neutral-50">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button 
                    onclick="switchTab('profile')"
                    id="tab-profile"
                    class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-center border-b-2 border-transparent hover:text-primary hover:border-primary/30 transition-all duration-200"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile Information
                    </span>
                </button>
                <button 
                    onclick="switchTab('password')"
                    id="tab-password"
                    class="tab-button flex-1 px-6 py-4 text-sm font-semibold text-center border-b-2 border-transparent hover:text-primary hover:border-primary/30 transition-all duration-200"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Change Password
                    </span>
                </button>
            </nav>
        </div>
        
        <div class="p-6">
            <!-- Password Required Alert -->
            @if(empty($user->password) || session('password_required'))
                <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-yellow-800 mb-1">Password Required</h3>
                            <p class="text-sm text-yellow-700">Please set a password to continue using the admin panel. You can set it in the "Change Password" tab below.</p>
                        </div>
                    </div>
                </div>
                <script>
                    // Auto-switch to password tab if password is required
                    document.addEventListener('DOMContentLoaded', function() {
                        if (window.location.hash === '#password' || {{ empty($user->password) ? 'true' : 'false' }}) {
                            switchTab('password');
                        }
                    });
                </script>
            @endif
            
            <!-- Success Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            
            <!-- Profile Tab Content -->
            <div id="content-profile" class="tab-content">
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Profile Photo -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Profile Photo</label>
                        <div class="flex items-center gap-6">
                            <div class="flex-shrink-0">
                                @if($user->photo)
                                    <img src="{{ Storage::disk('public')->url($user->photo) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-neutral-200 shadow-sm">
                                @else
                                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary-light/20 flex items-center justify-center border-4 border-neutral-200 shadow-sm">
                                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/gif" 
                                       class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light file:cursor-pointer transition">
                                <p class="mt-2 text-xs text-neutral-500">JPG, PNG or GIF</p>
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required 
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               placeholder="your.email@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" required 
                               value="{{ old('phone', $user->phone ? (strpos($user->phone, '880') === 0 ? '0' . substr($user->phone, 3) : $user->phone) : '') }}"
                               placeholder="01XXXXXXXXX"
                               class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        <p class="mt-1 text-xs text-neutral-500">Enter your 11-digit mobile number</p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Address -->
                    <div class="mb-6">
                        <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="3" 
                                  class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none"
                                  placeholder="Enter your address">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-neutral-200">
                        <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 border border-neutral-300 rounded-lg text-neutral-700 hover:bg-neutral-50 transition font-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-light transition font-semibold shadow-sm hover:shadow-md">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Password Tab Content -->
            <div id="content-password" class="tab-content hidden">
                <form method="POST" action="{{ route('admin.profile.update-password') }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Password (only if user has a password) -->
                    @if($user->password)
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-neutral-700 mb-2">Current Password <span class="text-red-500">*</span></label>
                        <input type="password" name="current_password" id="current_password" required 
                               class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               placeholder="Enter your current password">
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
                    
                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">New Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required 
                               class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               placeholder="Enter your new password">
                        <p class="mt-1 text-xs text-neutral-500">Password must be at least 8 characters long</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm New Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                               class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               placeholder="Confirm your new password">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-neutral-200">
                        <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 border border-neutral-300 rounded-lg text-neutral-700 hover:bg-neutral-50 transition font-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-light transition font-semibold shadow-sm hover:shadow-md">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Get active tab from URL hash or default to profile
    function getActiveTab() {
        const hash = window.location.hash;
        if (hash === '#password') return 'password';
        return 'profile';
    }
    
    function switchTab(tab) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('text-primary', 'border-primary');
            button.classList.add('text-neutral-600', 'border-transparent');
        });
        
        // Show selected tab content
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        // Add active state to selected tab
        const activeButton = document.getElementById('tab-' + tab);
        activeButton.classList.remove('text-neutral-600', 'border-transparent');
        activeButton.classList.add('text-primary', 'border-primary');
        
        // Update URL hash without scrolling
        window.history.replaceState(null, null, '#' + tab);
    }
    
    // Initialize tabs on page load
    document.addEventListener('DOMContentLoaded', function() {
        const activeTab = getActiveTab();
        switchTab(activeTab);
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('hashchange', function() {
        const activeTab = getActiveTab();
        switchTab(activeTab);
    });
</script>

<style>
    .tab-button {
        position: relative;
    }
    
    .tab-button.active {
        color: rgb(var(--primary));
        border-bottom-color: rgb(var(--primary));
    }
    
    .tab-content {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection
