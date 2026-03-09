@extends('layouts.admin')

@section('title', 'Job Applications')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Job Applications</h1>
        <p class="text-neutral-600 mt-1 text-sm">View and manage job applications</p>
    </div>
    <nav class="flex flex-wrap gap-2" aria-label="Status tabs">
        @php
            $statusTabs = [
                '' => 'All',
                'pending' => 'Pending',
                'shortlisted' => 'Shortlisted',
                'rejected' => 'Rejected',
                'hired' => 'Hired',
            ];
            $currentStatus = request('status', '');
        @endphp
        @foreach($statusTabs as $value => $label)
            <a href="{{ route('admin.job-applications.index', array_merge(request()->query(), ['status' => $value, 'page' => null])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium {{ ($currentStatus === (string)$value) ? 'bg-primary text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4">
    <form method="GET" action="{{ route('admin.job-applications.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="min-w-[200px]">
            <label for="job_circular_id" class="block text-sm font-medium text-neutral-700 mb-1">Job</label>
            <select name="job_circular_id" id="job_circular_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All Jobs</option>
                @foreach($jobCirculars as $jc)
                    <option value="{{ $jc->id }}" {{ request('job_circular_id') == $jc->id ? 'selected' : '' }}>{{ $jc->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[120px]">
            <label for="status" class="block text-sm font-medium text-neutral-700 mb-1">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-neutral-700 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, email, phone..."
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Job</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Email / Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Applied</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse($applications as $app)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 font-medium text-neutral-900">{{ $app->name }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600">{{ $app->jobCircular->title }}</td>
                    <td class="px-6 py-4 text-sm">{{ $app->email }}<br><span class="text-neutral-500">{{ $app->phone }}</span></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-xs rounded {{ $app->status === 'pending' ? 'bg-amber-100' : ($app->status === 'shortlisted' ? 'bg-blue-100' : ($app->status === 'hired' ? 'bg-green-100' : 'bg-red-100')) }}">{{ ucfirst($app->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-500">{{ $app->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        @can('jobApplications.view')
                        <a href="{{ route('admin.job-applications.show', $app) }}" class="text-primary hover:underline text-sm">View</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-neutral-500">No applications found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-neutral-200">{{ $applications->links() }}</div>
</div>
@endsection
