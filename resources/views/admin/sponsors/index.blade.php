@extends('layouts.admin')

@section('title', 'Sponsors')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@section('content')
@php
    $bulkMode = !request('trashed') && auth()->user()->can('sponsors.update');
@endphp
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
    <div class="flex items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Partners</h1>
            <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Manage partner sponsors</p>
        </div>
        <nav class="flex gap-2">
            <a href="{{ route('admin.sponsors.index', array_merge(request()->query(), ['trashed' => 0])) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ !request('trashed') ? 'bg-primary text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">Active</a>
            <a href="{{ route('admin.sponsors.index', array_merge(request()->query(), ['trashed' => 1])) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ request('trashed') ? 'bg-neutral-500 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">Deleted</a>
        </nav>
    </div>
    @if(!request('trashed'))
    <a href="{{ route('admin.sponsors.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base text-center">
        + Create Sponsor
    </a>
    @endif
</div>

@if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 text-sm">{{ session('success') }}</div>
@endif

<!-- Search Form -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.sponsors.index') }}" class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-4 sm:items-end">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search Partners</label>
            <input 
                type="text" 
                name="search" 
                id="search" 
                value="{{ request('search') }}" 
                placeholder="Search by name, phone, address, or partner code..."
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
            <a href="{{ route('admin.sponsors.index') }}{{ request('per_page') ? '?per_page=' . request('per_page') : '' }}" class="bg-neutral-200 text-neutral-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base">
                Clear
            </a>
            @endif
        </div>
        </div>
        @if(request('trashed'))<input type="hidden" name="trashed" value="1">@endif
        @if(request('per_page') && !request('search'))<input type="hidden" name="per_page" value="{{ request('per_page') }}">@endif
    </form>
</div>

{{-- Single bulk form (referrer + level); checkboxes use form="…" so row Delete forms stay separate. Level actions use button formaction. --}}
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($bulkMode)
    <form id="sponsors-bulk-form" method="POST" action="{{ route('admin.sponsors.bulk-set-referrer') }}" class="block border-b border-neutral-200 bg-neutral-50/90">
        @csrf
        <div class="flex flex-col gap-4 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:gap-4 border-b border-neutral-200/80 pb-4">
                <div class="flex-1 min-w-0 sm:max-w-xl">
                    <span class="block text-sm font-medium text-neutral-700 mb-1">Bulk: referrer for checked partners</span>
                    <x-admin.sponsor-referrer-picker
                        :referrers="$bulkReferrerOptions"
                        name="referrer_sponsor_id"
                        :selected="null"
                        :show-label="false"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" name="bulk_action" value="assign" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-light transition">
                        Assign referrer
                    </button>
                    <button type="submit" name="bulk_action" value="clear" class="px-4 py-2 border border-neutral-300 rounded-lg text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition" onclick="return confirm('Clear referrer for all checked partners?');">
                        Clear referrer
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:gap-4">
                <div class="flex-1 min-w-0 sm:max-w-md">
                    <label for="bulk_sponsor_level_id" class="block text-sm font-medium text-neutral-700 mb-1">Bulk: sponsor level for checked partners</label>
                    <select name="bulk_sponsor_level_id" id="bulk_sponsor_level_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm bg-white">
                        <option value="">— Select level —</option>
                        @foreach($bulkSponsorLevels as $lvl)
                            <option value="{{ $lvl->id }}" {{ (string) old('bulk_sponsor_level_id') === (string) $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->name }} (rank {{ $lvl->rank }}, {{ number_format($lvl->commission_percent, 2) }}%)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" formaction="{{ route('admin.sponsors.bulk-set-level') }}" name="bulk_level_action" value="assign" class="px-4 py-2 bg-sky-700 text-white rounded-lg text-sm font-semibold hover:bg-sky-800 transition">
                        Assign level
                    </button>
                    <button type="submit" formaction="{{ route('admin.sponsors.bulk-set-level') }}" name="bulk_level_action" value="clear" class="px-4 py-2 border border-neutral-300 rounded-lg text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition" onclick="return confirm('Clear sponsor level for all checked partners? They will use legacy referral commission rules.');">
                        Clear level
                    </button>
                </div>
            </div>
            @error('sponsor_ids')
                <p class="w-full text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('referrer_sponsor_id')
                <p class="w-full text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('bulk_action')
                <p class="w-full text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('bulk_level_action')
                <p class="w-full text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('bulk_sponsor_level_id')
                <p class="w-full text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="w-full text-xs text-neutral-500 lg:hidden">Bulk checkboxes appear in the table below; scroll horizontally or use a larger screen to select partners.</p>
        </div>
    </form>
    @endif
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    @if($bulkMode)
                    <th scope="col" class="px-4 py-3 text-left w-12">
                        <input type="checkbox" id="sponsor-select-all" class="rounded border-neutral-300 text-primary focus:ring-primary" title="Select all on this page">
                    </th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Partner Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Join Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Referred by</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Referrals</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Total Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Total Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($sponsors as $sponsor)
                <tr class="hover:bg-neutral-50">
                    @if($bulkMode)
                    <td class="px-4 py-4 whitespace-nowrap align-top">
                        <input type="checkbox" name="sponsor_ids[]" value="{{ $sponsor->id }}" form="sponsors-bulk-form" class="sponsor-bulk-check rounded border-neutral-300 text-primary focus:ring-primary">
                    </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($sponsor->photo)
                            <img src="{{ Storage::disk('public')->url($sponsor->photo) }}" alt="{{ $sponsor->name }}" class="w-20 h-20 aspect-square object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-neutral-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-primary hover:underline">{{ $sponsor->name }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 font-mono">
                        {{ $sponsor->affiliate_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $sponsor->phone ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-500 max-w-xs truncate" title="{{ $sponsor->address ?? 'N/A' }}">
                        {{ $sponsor->address ? Str::limit($sponsor->address, 30) : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $sponsor->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($sponsor->sponsor)
                            @if($sponsor->sponsor->role === 'sponsor')
                                <a href="{{ route('admin.sponsors.show', $sponsor->sponsor) }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">{{ $sponsor->sponsor->name }}</a>
                            @else
                                <span class="text-neutral-700">{{ $sponsor->sponsor->name }}</span>
                            @endif
                        @else
                            <span class="text-neutral-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $sponsor->referrals_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $sponsor->orders_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-accent">৳{{ number_format($sponsor->total_revenue, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if(request('trashed') && $sponsor->trashed())
                            <div class="flex flex-wrap items-center gap-2">
                                @can('sponsors.restore')
                                <form action="{{ route('admin.sponsors.restore', $sponsor) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-700 font-medium">Restore</button>
                                </form>
                                @endcan
                                @can('sponsors.forceDelete')
                                <form action="{{ route('admin.sponsors.force-delete', $sponsor) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this partner? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 hover:text-red-800 font-medium">Delete forever</button>
                                </form>
                                @endcan
                            </div>
                        @else
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-primary hover:text-primary-light font-medium">View</a>
                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                            @can('sponsors.update')
                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}?promote=level" class="text-amber-700 hover:text-amber-900 font-medium">Promote</a>
                            @endcan
                            <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $bulkMode ? 12 : 11 }}" class="px-6 py-4 text-center text-neutral-500">
                        @if(request('search'))
                            No sponsors found matching "{{ request('search') }}"
                        @else
                            No sponsors found
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($sponsors as $sponsor)
        <div class="p-4 hover:bg-neutral-50 transition-colors">
            <div class="flex items-start gap-4 mb-3">
                @if($bulkMode)
                <div class="flex-shrink-0 pt-1">
                    <input type="checkbox" name="sponsor_ids[]" value="{{ $sponsor->id }}" form="sponsors-bulk-form" class="sponsor-bulk-check rounded border-neutral-300 text-primary focus:ring-primary w-5 h-5" aria-label="Select {{ $sponsor->name }} for bulk actions">
                </div>
                @endif
                <div class="flex-shrink-0">
                    @if($sponsor->photo)
                        <img src="{{ Storage::disk('public')->url($sponsor->photo) }}" alt="{{ $sponsor->name }}" class="w-16 h-16 rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-neutral-200 flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-primary hover:underline">
                        <h3 class="text-sm font-semibold text-neutral-900 truncate mb-1">{{ $sponsor->name }}</h3>
                    </a>
                    <p class="text-xs text-neutral-500 font-mono mb-2">{{ $sponsor->affiliate_code }}</p>
                    @if($sponsor->sponsor)
                    <p class="text-xs mb-2">
                        <span class="text-neutral-500">Referred by:</span>
                        @if($sponsor->sponsor->role === 'sponsor')
                            <a href="{{ route('admin.sponsors.show', $sponsor->sponsor) }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">{{ $sponsor->sponsor->name }}</a>
                        @else
                            <span class="text-neutral-700 font-medium">{{ $sponsor->sponsor->name }}</span>
                        @endif
                    </p>
                    @endif
                    <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                        <div>
                            <span class="text-neutral-500">Phone:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $sponsor->phone ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-500">Joined:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $sponsor->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-500">Referrals:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $sponsor->referrals_count }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-500">Orders:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $sponsor->orders_count }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-neutral-500">Revenue:</span>
                            <span class="text-green-600 font-semibold ml-1">৳{{ number_format($sponsor->total_revenue, 2) }}</span>
                        </div>
                    </div>
                    @if($sponsor->address)
                    <p class="text-xs text-neutral-500 mb-3 truncate" title="{{ $sponsor->address }}">{{ $sponsor->address }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-neutral-200">
                        @if(request('trashed') && $sponsor->trashed())
                            <div class="flex flex-wrap items-center gap-2">
                                @can('sponsors.restore')
                                <form action="{{ route('admin.sponsors.restore', $sponsor) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 font-medium text-sm">Restore</button>
                                </form>
                                @endcan
                                @can('sponsors.forceDelete')
                                <form action="{{ route('admin.sponsors.force-delete', $sponsor) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this partner? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 hover:text-red-800 font-medium text-sm">Delete forever</button>
                                </form>
                                @endcan
                            </div>
                        @else
                        <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-primary hover:text-primary-light font-medium text-sm">View</a>
                        <span class="text-neutral-300">|</span>
                        <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Edit</a>
                        @can('sponsors.update')
                        <span class="text-neutral-300">|</span>
                        <a href="{{ route('admin.sponsors.edit', $sponsor) }}?promote=level" class="text-amber-700 hover:text-amber-900 font-medium text-sm">Promote</a>
                        @endcan
                        <span class="text-neutral-300">|</span>
                        <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm">Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">
            @if(request('search'))
                No sponsors found matching "{{ request('search') }}"
            @else
                No sponsors found
            @endif
        </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($sponsors->hasPages())
<div class="mt-4">
    {{ $sponsors->links() }}
</div>
@endif

@if($bulkMode)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var master = document.getElementById('sponsor-select-all');
    if (!master) return;
    var boxes = function () { return document.querySelectorAll('.sponsor-bulk-check'); };
    master.addEventListener('change', function () {
        boxes().forEach(function (cb) { cb.checked = master.checked; });
    });
    boxes().forEach(function (cb) {
        cb.addEventListener('change', function () {
            var all = document.querySelectorAll('.sponsor-bulk-check');
            var on = document.querySelectorAll('.sponsor-bulk-check:checked');
            master.checked = all.length > 0 && on.length === all.length;
            master.indeterminate = on.length > 0 && on.length < all.length;
        });
    });
});
</script>
@endpush
@endif
@endsection
