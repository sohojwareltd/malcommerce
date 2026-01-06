@extends('layouts.sponsor')

@section('title', 'My Referrals')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">My Referrals</h1>
            <p class="text-neutral-600 mt-1">Manage and view all your referred users</p>
        </div>
        <a href="{{ route('sponsor.users.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
            + Add New User
        </a>
    </div>
</div>

<!-- Search and Filter Form -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('sponsor.users.index') }}" class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-4 sm:items-end">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search Referrals</label>
                <input 
                    type="text" 
                    name="search" 
                    id="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by name, phone, address, or affiliate code..."
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base"
                >
            </div>
            <div class="sm:w-40">
                <label for="per_page" class="block text-sm font-medium text-neutral-700 mb-2">Per Page</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                    <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base flex-1 sm:flex-none">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('sponsor.users.index') }}{{ request('per_page') ? '?per_page=' . request('per_page') : '' }}" class="bg-neutral-200 text-neutral-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base">
                    Clear
                </a>
                @endif
            </div>
        </div>
        <!-- Preserve per_page when clearing search -->
        @if(request('per_page') && !request('search'))
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Affiliate Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($referrals as $referral)
                <tr class="hover:bg-neutral-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($referral->photo)
                            <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-neutral-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-neutral-900">{{ $referral->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-neutral-500">{{ $referral->phone ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-mono font-semibold text-primary bg-primary/10 px-2 py-1 rounded">{{ $referral->affiliate_code }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-500 max-w-xs truncate" title="{{ $referral->address ?? 'N/A' }}">
                        {{ $referral->address ? Str::limit($referral->address, 30) : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-neutral-900">{{ $referral->orders_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $referral->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('sponsor.users.show', $referral) }}" class="text-primary hover:text-primary-light font-medium">View</a>
                            <a href="{{ route('sponsor.users.edit', $referral) }}" class="text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-neutral-500">
                        @if(request('search'))
                            No referrals found matching "{{ request('search') }}"
                        @else
                            No referrals yet. <a href="{{ route('sponsor.users.create') }}" class="text-primary hover:underline">Add your first referral</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($referrals as $referral)
        <div class="p-4 hover:bg-neutral-50 transition-colors">
            <div class="flex items-start gap-4 mb-3">
                <div class="flex-shrink-0">
                    @if($referral->photo)
                        <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-16 h-16 rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-neutral-200 flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('sponsor.users.show', $referral) }}" class="text-primary hover:underline">
                        <h3 class="text-sm font-semibold text-neutral-900 truncate mb-1">{{ $referral->name }}</h3>
                    </a>
                    <p class="text-xs text-neutral-500 font-mono mb-2">{{ $referral->affiliate_code }}</p>
                    <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                        <div>
                            <span class="text-neutral-500">Phone:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $referral->phone ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-500">Orders:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $referral->orders_count }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-neutral-500">Joined:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $referral->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    @if($referral->address)
                    <p class="text-xs text-neutral-500 mb-3 truncate" title="{{ $referral->address }}">{{ $referral->address }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-neutral-200">
                        <a href="{{ route('sponsor.users.show', $referral) }}" class="text-primary hover:text-primary-light font-medium text-sm">View</a>
                        <span class="text-neutral-300">|</span>
                        <a href="{{ route('sponsor.users.edit', $referral) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">
            @if(request('search'))
                No referrals found matching "{{ request('search') }}"
            @else
                <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-sm text-neutral-500 font-medium">No referrals yet</p>
                <p class="text-xs text-neutral-400 mt-1"><a href="{{ route('sponsor.users.create') }}" class="text-primary hover:underline">Add your first referral</a></p>
            @endif
        </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($referrals->hasPages())
<div class="mt-4">
    {{ $referrals->links() }}
</div>
@endif
@endsection

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp


