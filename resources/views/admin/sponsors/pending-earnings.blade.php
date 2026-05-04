@extends('layouts.admin')

@section('title', 'Partners by pending purchase earnings')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Pending purchase earnings</h1>
        <p class="text-sm text-neutral-600 mt-1">
            Partners with <strong>non-zero</strong> estimated commission from pending requests in scope (approved today at current rates).
            <span class="font-medium text-neutral-800">{{ $rangeLabel }}</span>
            · <span class="tabular-nums">{{ $pendingPurchaseCount }}</span> request(s) in scope.
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.sponsors.index') }}" class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition">
            All partners
        </a>
        <a href="{{ route('admin.sponsors.leaderboard') }}" class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition">
            Referral leaderboard
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-4 sm:p-6 mb-6">
    <form method="GET" action="{{ route('admin.sponsors.pending-earnings') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="sort" class="block text-sm font-medium text-neutral-700 mb-1">Sort by pending (est.)</label>
                <select name="sort" id="sort" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                    <option value="desc" {{ $sort === 'desc' ? 'selected' : '' }}>Highest first</option>
                    <option value="asc" {{ $sort === 'asc' ? 'selected' : '' }}>Lowest first</option>
                </select>
            </div>
            <div>
                <label for="month" class="block text-sm font-medium text-neutral-700 mb-1">Limit to month (optional)</label>
                <input type="month" name="month" id="month" value="{{ request('month') }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                <p class="text-xs text-neutral-500 mt-1">Only pending purchases <strong>created</strong> in this month.</p>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-neutral-700 mb-1">From date (optional)</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-1">To date (optional)</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label for="per_page" class="block text-sm font-medium text-neutral-700 mb-1">Rows per page</label>
                <select name="per_page" id="per_page" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                    @foreach([15, 30, 50, 100] as $size)
                        <option value="{{ $size }}" {{ (int) request('per_page', 30) === $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <p class="text-xs text-neutral-500">If <strong>month</strong> is set, it overrides the date range. Clear month to use from/to only.</p>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="inline-flex rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-light transition">Apply</button>
            <a href="{{ route('admin.sponsors.pending-earnings') }}" class="inline-flex rounded-lg border border-neutral-300 px-5 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Partner</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Phone</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Code</th>
                    <th class="px-4 py-3 text-right font-semibold text-neutral-600">Pending (est.)</th>
                    <th class="px-4 py-3 text-right font-semibold text-neutral-600">Balance</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($paginator as $i => $sponsor)
                    <tr class="hover:bg-neutral-50/80">
                        <td class="px-4 py-3 text-neutral-600">{{ $paginator->firstItem() + $i }}</td>
                        <td class="px-4 py-3 font-semibold text-neutral-900">{{ $sponsor->name }}</td>
                        <td class="px-4 py-3 text-neutral-700">{{ $sponsor->phone ?: '—' }}</td>
                        <td class="px-4 py-3 font-mono text-neutral-700">{{ $sponsor->affiliate_code ?: '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold tabular-nums text-violet-800">৳{{ number_format($sponsor->pending_purchase_est ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums text-neutral-900">৳{{ number_format($sponsor->balance ?? 0, 2) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.sponsors.show', $sponsor) }}#income" class="text-primary font-semibold hover:underline">Profile</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-neutral-500">No partners with estimated pending commission in this scope. Try another month/date range, or all requests may be outside the filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($paginator as $i => $sponsor)
            <div class="p-4">
                <p class="font-semibold text-neutral-900">{{ $sponsor->name }}</p>
                <p class="text-xs text-neutral-500 mt-1">#{{ $paginator->firstItem() + $i }} · {{ $sponsor->affiliate_code ?: '—' }}</p>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <div><span class="text-neutral-500">Pending:</span> <span class="font-semibold text-violet-800 tabular-nums">৳{{ number_format($sponsor->pending_purchase_est ?? 0, 2) }}</span></div>
                    <div><span class="text-neutral-500">Balance:</span> <span class="tabular-nums">৳{{ number_format($sponsor->balance ?? 0, 2) }}</span></div>
                </div>
                <a href="{{ route('admin.sponsors.show', $sponsor) }}#income" class="mt-3 inline-block text-sm font-semibold text-primary hover:underline">Open profile</a>
            </div>
        @empty
            <div class="p-8 text-center text-neutral-500 text-sm">No partners with estimated pending commission in this scope.</div>
        @endforelse
    </div>

    @if($paginator->hasPages())
        <div class="border-t border-neutral-100 px-4 py-3">{{ $paginator->links() }}</div>
    @endif
</div>
@endsection
