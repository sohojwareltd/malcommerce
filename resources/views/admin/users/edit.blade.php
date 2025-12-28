@extends('layouts.admin')

@section('title', 'Edit Admin User')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Edit Admin User</h1>
            <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Update admin user information</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm sm:text-base">
            ← Back
        </a>
    </div>
</div>

<!-- Edit Admin User Form -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4 sm:space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base @error('name') border-red-500 @enderror"
                    placeholder="Enter admin name"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $user->phone) }}" 
                    required
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base @error('phone') border-red-500 @enderror"
                    placeholder="01712345678"
                >
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-neutral-500">Enter 11-digit Bangladesh mobile number</p>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4 border-t border-neutral-200">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base">
                Update Admin User
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

