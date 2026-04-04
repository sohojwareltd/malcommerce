@extends('layouts.admin')

@section('title', 'Sponsor levels')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Sponsor levels</h1>
        <p class="text-neutral-600 mt-1 text-sm sm:text-base">Rank 0 is the top anchor; larger rank is deeper. Commission % splits referral payouts differentially up the upline.</p>
    </div>
    @can('create', \App\Models\SponsorLevel::class)
    <a href="{{ route('admin.sponsor-levels.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-center text-sm sm:text-base">
        + Add level
    </a>
    @endcan
</div>

@if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-800 text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Rank</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Commission %</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Default for new</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Sponsors</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse($levels as $level)
                <tr class="hover:bg-neutral-50">
                    <td class="px-4 py-3 font-medium text-neutral-900">{{ $level->name }}</td>
                    <td class="px-4 py-3 tabular-nums">{{ $level->rank }}</td>
                    <td class="px-4 py-3 tabular-nums">{{ number_format($level->commission_percent, 2) }}%</td>
                    <td class="px-4 py-3">
                        @if($level->is_default_for_new)
                            <span class="text-emerald-700 font-medium text-sm">Yes</span>
                        @else
                            <span class="text-neutral-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 tabular-nums">{{ $level->users_count }}</td>
                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                        @can('update', $level)
                        <a href="{{ route('admin.sponsor-levels.edit', $level) }}" class="text-primary hover:underline text-sm font-semibold">Edit</a>
                        @endcan
                        @can('delete', $level)
                        <form action="{{ route('admin.sponsor-levels.destroy', $level) }}" method="POST" class="inline" onsubmit="return confirm('Delete this level?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-neutral-500">No levels defined.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($levels as $level)
            <div class="p-4 hover:bg-neutral-50 transition-colors">
                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-neutral-900">{{ $level->name }}</h3>
                    <p class="text-xs text-neutral-500 mt-1">Rank {{ $level->rank }} · {{ number_format($level->commission_percent, 2) }}% commission</p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                    <div>
                        <span class="text-neutral-500">Sponsors</span>
                        <p class="text-neutral-900 font-medium tabular-nums">{{ $level->users_count }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-500">Default for new</span>
                        <p class="font-medium text-sm {{ $level->is_default_for_new ? 'text-emerald-700' : 'text-neutral-400' }}">
                            {{ $level->is_default_for_new ? 'Yes' : '—' }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 pt-3 border-t border-neutral-200">
                    @can('update', $level)
                        <a href="{{ route('admin.sponsor-levels.edit', $level) }}" class="text-primary hover:underline text-sm font-semibold">Edit</a>
                    @endcan
                    @can('delete', $level)
                        <form action="{{ route('admin.sponsor-levels.destroy', $level) }}" method="POST" class="inline" onsubmit="return confirm('Delete this level?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-neutral-500 text-sm">No levels defined.</div>
        @endforelse
    </div>
</div>
@endsection
