@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6 font-bangla">নতুন পাসওয়ার্ড সেট করুন</h2>
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600 font-bangla">{{ session('error') }}</p>
            </div>
        @endif
        
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-neutral-700 mb-2 font-bangla">নতুন পাসওয়ার্ড</label>
                <input type="password" name="password" id="password" required autofocus 
                       placeholder="Enter new password"
                       minlength="8"
                       class="w-full px-4 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-neutral-300' }} rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="mt-1 text-xs text-neutral-500 font-bangla">পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2 font-bangla">পাসওয়ার্ড নিশ্চিত করুন</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required 
                       placeholder="Confirm new password"
                       minlength="8"
                       class="w-full px-4 py-2 border {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-neutral-300' }} rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition font-bangla">
                পাসওয়ার্ড রিসেট করুন
            </button>
        </form>
        
        <p class="mt-6 text-center text-sm text-neutral-600 font-bangla">
            <a href="{{ route('login') }}" class="text-primary hover:underline">লগইন পেজে ফিরে যান</a>
        </p>
    </div>
</div>
@endsection
