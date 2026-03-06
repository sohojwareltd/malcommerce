@extends('layouts.admin')

@section('title', 'Profile Settings')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="max-w-4xl">
    {{-- Page Header --}}
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Profile Settings</h1>
                <p class="text-neutral-600 mt-1 text-sm sm:text-base">Manage your account information and security</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm font-medium self-start sm:self-auto">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(empty($user->password) || session('password_required'))
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50/80 p-4 sm:p-5">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-amber-900">Password Required</h3>
                    <p class="text-sm text-amber-800 mt-1">Set a password to secure your admin account. Use the "Security" tab below.</p>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.location.hash === '#security' || {{ empty($user->password) ? 'true' : 'false' }}) {
                    switchTab('security');
                }
            });
        </script>
    @endif

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 sm:p-5">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="mb-6">
        <nav class="flex gap-1 p-1 bg-neutral-100 rounded-xl w-fit" role="tablist">
            <button type="button" id="tab-profile" onclick="switchTab('profile')" role="tab" aria-selected="true" aria-controls="content-profile"
                class="tab-btn px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2 bg-white text-primary shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
            </button>
            <button type="button" id="tab-security" onclick="switchTab('security')" role="tab" aria-selected="false" aria-controls="content-security"
                class="tab-btn px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2 text-neutral-600 hover:text-neutral-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Security
            </button>
        </nav>
    </div>

    {{-- Profile Tab --}}
    <div id="content-profile" class="tab-panel" role="tabpanel">
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-lg font-semibold text-neutral-900 mb-6">Personal Information</h2>

                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Avatar --}}
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-neutral-700 mb-3">Profile Photo</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                            <label for="photo" class="group relative block cursor-pointer flex-shrink-0">
                                <div class="w-24 h-24 rounded-full overflow-hidden ring-2 ring-neutral-200 group-hover:ring-primary/50 transition-all duration-200 bg-neutral-100">
                                    @if($user->photo)
                                        <img src="{{ Storage::disk('public')->url($user->photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-neutral-200 to-neutral-300">
                                            <svg class="w-10 h-10 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">Change</span>
                                </div>
                                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/gif" class="sr-only">
                            </label>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-neutral-500">JPG, PNG or GIF. Max 2MB.</p>
                                @error('photo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                                class="input-field w-full px-4 py-2.5 rounded-lg border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition @error('name') border-red-500 focus:ring-red-500/20 focus:border-red-500 @enderror"
                                placeholder="Your name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" required
                                value="{{ old('phone', $user->phone ? (strpos($user->phone, '880') === 0 ? '0' . substr($user->phone, 3) : $user->phone) : '') }}"
                                placeholder="01XXXXXXXXX"
                                class="input-field w-full px-4 py-2.5 rounded-lg border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition @error('phone') border-red-500 focus:ring-red-500/20 focus:border-red-500 @enderror">
                            <p class="mt-2 text-xs text-neutral-500">11-digit Bangladesh mobile number</p>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
                            <textarea name="address" id="address" rows="3" placeholder="Your address"
                                class="input-field w-full px-4 py-2.5 rounded-lg border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none @error('address') border-red-500 focus:ring-red-500/20 focus:border-red-500 @enderror">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row gap-3 sm:justify-end pt-8 mt-8 border-t border-neutral-200">
                        <a href="{{ route('admin.dashboard') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-center font-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary px-6 py-2.5 rounded-lg font-semibold text-white bg-primary hover:bg-primary-light transition shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Security Tab --}}
    <div id="content-security" class="tab-panel hidden" role="tabpanel">
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-lg font-semibold text-neutral-900 mb-6">Change Password</h2>

                <form method="POST" action="{{ route('admin.profile.update-password') }}">
                    @csrf
                    @method('PUT')

                    @if($user->password)
                        <div class="mb-5">
                            <label for="current_password" class="block text-sm font-medium text-neutral-700 mb-2">Current Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" required
                                    placeholder="Enter current password"
                                    class="input-field w-full px-4 py-2.5 pr-12 rounded-lg border border-neutral-300 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition @error('current_password') border-red-500 @enderror">
                                <button type="button" onclick="togglePassword('current_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-neutral-500 hover:text-neutral-700 rounded" aria-label="Toggle visibility">
                                    <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50/80 p-4">
                            <p class="text-sm text-blue-800">No password set yet. Set one below to enable password-based login.</p>
                        </div>
                    @endif

                    <div class="space-y-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">New Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required
                                    placeholder="Enter new password"
                                    class="input-field w-full px-4 py-2.5 pr-12 rounded-lg border border-neutral-300 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition @error('password') border-red-500 @enderror">
                                <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-neutral-500 hover:text-neutral-700 rounded" aria-label="Toggle visibility">
                                    <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-neutral-500">At least 8 characters</p>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    placeholder="Confirm new password"
                                    class="input-field w-full px-4 py-2.5 pr-12 rounded-lg border border-neutral-300 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition @error('password_confirmation') border-red-500 @enderror">
                                <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-neutral-500 hover:text-neutral-700 rounded" aria-label="Toggle visibility">
                                    <svg class="eye-open w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="eye-closed w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row gap-3 sm:justify-end pt-8 mt-8 border-t border-neutral-200">
                        <a href="{{ route('admin.dashboard') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-center font-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary px-6 py-2.5 rounded-lg font-semibold text-white bg-primary hover:bg-primary-light transition shadow-sm">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function getActiveTab() {
        return window.location.hash === '#security' ? 'security' : 'profile';
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-primary', 'shadow-sm');
            btn.classList.add('text-neutral-600', 'hover:text-neutral-900');
            btn.setAttribute('aria-selected', 'false');
        });
        document.getElementById('content-' + tab).classList.remove('hidden');
        const active = document.getElementById('tab-' + tab);
        active.classList.remove('text-neutral-600');
        active.classList.add('bg-white', 'text-primary', 'shadow-sm');
        active.setAttribute('aria-selected', 'true');
        window.history.replaceState(null, null, '#' + tab);
    }

    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const open = btn.querySelector('.eye-open');
        const closed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            open.classList.add('hidden');
            closed.classList.remove('hidden');
        } else {
            input.type = 'password';
            open.classList.remove('hidden');
            closed.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => switchTab(getActiveTab()));
    window.addEventListener('hashchange', () => switchTab(getActiveTab()));
</script>
@endpush

<style>
    .btn-secondary { background: #f4f4f5; color: #3f3f46; }
    .btn-secondary:hover { background: #e4e4e7; }
</style>
@endsection
