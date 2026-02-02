@extends('layouts.sponsor')

@section('title', 'My Referrals')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

<div class="min-h-screen pb-6" style="">
    <!-- Header -->
    <div class="app-card mx-4 mt-4 mb-4 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
            <div>
                <h1 class="text-base sm:text-lg md:text-xl font-bold" style="color: var(--color-dark);">My Referrals</h1>
                <p class="text-xs sm:text-sm mt-1" style="color: var(--color-medium);">Manage and view all your referred users</p>
            </div>
            <a href="{{ route('sponsor.users.create') }}" class="px-4 py-2 rounded-lg text-white text-xs sm:text-sm font-semibold whitespace-nowrap" style="background: var(--color-medium);">
                + Add New User
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="app-card mx-4 mb-4 p-3 sm:p-4">
        <form method="GET" action="{{ route('sponsor.users.index') }}" class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-3 w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search by name, phone, address, or code..."
                        class="w-full pl-10 pr-4 py-2 rounded-xl border-2 text-xs sm:text-sm focus:outline-none"
                        style="border-color: var(--color-accent);"
                    >
                </div>
                <div class="sm:w-32">
                    <select name="per_page" id="per_page" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 rounded-xl border-2 text-xs sm:text-sm focus:outline-none"
                            style="border-color: var(--color-accent);">
                        <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg text-white text-xs sm:text-sm font-semibold" style="background: var(--color-medium);">
                        Search
                    </button>
                    @if(request('search'))
                    <a href="{{ route('sponsor.users.index') }}{{ request('per_page') ? '?per_page=' . request('per_page') : '' }}" 
                       class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold" style="background: var(--color-accent); color: var(--color-dark);">
                        Clear
                    </a>
                    @endif
                </div>
            </div>
            @if(request('per_page') && !request('search'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
        </form>
    </div>

    <!-- Referrals List -->
    <div class="app-card mx-4 mb-4 overflow-hidden">
        @if($referrals->count() > 0)

        {{-- Desktop: Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                    @foreach($referrals as $referral)
                    <tr class="hover:bg-neutral-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($referral->photo)
                                <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--color-light);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <span class="text-sm font-medium text-neutral-900">{{ $referral->name }}</span>
                                @if($referral->address)
                                    <p class="text-xs text-neutral-500 truncate max-w-[200px]">{{ $referral->address }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono" style="color: var(--color-medium);">{{ $referral->affiliate_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">{{ $referral->orders_count ?? 0 }} orders</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $referral->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex gap-2 flex-wrap">
                                @if($referral->phone)
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $referral->phone) }}" class="font-medium inline-flex items-center gap-1" style="color: var(--color-medium);" title="Call">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1H9c-1.1 0-2-.9-2-2v-3.5c0-.55.45-1 1-1h1.5c0-1.25.2-2.45.57-3.57.11-.35.03-.74-.25-1.02l-2.2-2.2z"/></svg>
                                    Call
                                </a>
                                <span class="text-neutral-300">|</span>
                                @endif
                                <a href="{{ route('sponsor.users.edit', $referral) }}" class="font-medium" style="color: var(--color-medium);">Edit</a>
                                <span class="text-neutral-300">|</span>
                                <a href="{{ route('sponsor.users.show', $referral) }}" class="font-medium" style="color: var(--color-medium);">View</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: Card View --}}
        <div class="lg:hidden p-3 sm:p-4">
            <div class="space-y-4">
                @foreach($referrals as $referral)
                <x-sponsor.referral-card :referral="$referral" />
                @endforeach
            </div>
        </div>

        @else
        <div class="p-8 sm:p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            @if(request('search'))
                <p class="text-sm font-medium mb-2" style="color: var(--color-medium);">No referrals found</p>
                <p class="text-xs" style="color: var(--color-light);">Try a different search term</p>
            @else
                <p class="text-sm font-medium mb-2" style="color: var(--color-medium);">No referrals yet</p>
                <a href="{{ route('sponsor.users.create') }}" class="text-xs font-semibold inline-block mt-2" style="color: var(--color-medium);">Add your first referral →</a>
            @endif
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($referrals->hasPages())
    <div class="mx-4 mt-4">
        {{ $referrals->links() }}
    </div>
    @endif
</div>
@endsection
