@extends('layouts.admin')

@section('title', 'Edit Profile')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-primary-light px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Edit Profile</h1>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Profile Photo -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Profile Photo</label>
                    <div class="flex items-center gap-6">
                        <div class="flex-shrink-0">
                            @if($user->photo)
                                <img src="{{ Storage::disk('public')->url($user->photo) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-neutral-200">
                            @else
                                <div class="w-24 h-24 rounded-full bg-neutral-200 flex items-center justify-center border-4 border-neutral-200">
                                    <svg class="w-12 h-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/gif" 
                                   class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light file:cursor-pointer">
                            <p class="mt-1 text-xs text-neutral-500">JPG, PNG or GIF. Max size: 2MB</p>
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
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" required 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
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
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <p class="mt-1 text-xs text-neutral-500">Enter your 11-digit mobile number</p>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Address -->
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
                    <textarea name="address" id="address" rows="3" 
                              class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password Section -->
                <div class="border-t border-neutral-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-neutral-800 mb-4">Change Password</h3>
                    <p class="text-sm text-neutral-600 mb-4">Leave blank if you don't want to change your password</p>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">New Password</label>
                        <input type="password" name="password" id="password" 
                               class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t border-neutral-200">
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 border border-neutral-300 rounded-lg text-neutral-700 hover:bg-neutral-50 transition font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition font-semibold">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

