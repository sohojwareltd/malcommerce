@extends('layouts.admin')

@section('title', 'Sponsors')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Partners</h1>
        <p class="text-neutral-600 mt-2">Manage partner sponsors</p>
    </div>
    <a href="{{ route('admin.sponsors.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
        + Create Sponsor
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Partner Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Referrals</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Total Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Total Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($sponsors as $sponsor)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($sponsor->photo)
                            <img src="{{ Storage::disk('public')->url($sponsor->photo) }}" alt="{{ $sponsor->name }}" class="w-30 h-30 rounded aspect-square object-cover">
                        @else
                            <div class="w-20 h-20 rounded bg-neutral-200 flex items-center justify-center">
                                <svg class="w-10 h-10 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-primary hover:underline">{{ $sponsor->name }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                     {{ $sponsor->affiliate_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        {{ $sponsor->phone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 font-mono">{{ $sponsor->affiliate_code }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-500 max-w-xs truncate" title="{{ $sponsor->address ?? 'N/A' }}">
                        {{ $sponsor->address ? Str::limit($sponsor->address, 30) : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $sponsor->referrals_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $sponsor->orders_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-accent">৳{{ number_format($sponsor->total_revenue, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-primary hover:text-primary-light font-medium">View</a>
                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                            <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this sponsor? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-neutral-500">No sponsors found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


