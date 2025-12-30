@extends('layouts.admin')

@section('title', 'Change Password')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-primary-light px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Change Password</h1>
        </div>
        
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.profile.update-password') }}">
                @csrf
                @method('PUT')
                
                <!-- Current Password (only if user has a password) -->
                @if($user->password)
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-neutral-700 mb-2">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" id="current_password" required 
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Enter your current password">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">You don't have a password set yet. You can set one now.</p>
                </div>
                @endif
                
                <!-- New Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required 
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Enter your new password">
                    <p class="mt-1 text-xs text-neutral-500">Password must be at least 8 characters long</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Confirm New Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Confirm your new password">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t border-neutral-200">
                    <a href="{{ route('admin.profile.edit') }}" class="px-6 py-2 border border-neutral-300 rounded-lg text-neutral-700 hover:bg-neutral-50 transition font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition font-semibold">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

