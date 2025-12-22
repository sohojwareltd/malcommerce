@extends('layouts.admin')

@section('title', 'Sponsors')

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
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Partner Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Referrals</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Total Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Total Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($sponsors as $sponsor)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                        <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="text-primary hover:underline">{{ $sponsor->name }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $sponsor->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 font-mono">{{ $sponsor->affiliate_code }}</td>
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
                    <td colspan="7" class="px-6 py-4 text-center text-neutral-500">No sponsors found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


