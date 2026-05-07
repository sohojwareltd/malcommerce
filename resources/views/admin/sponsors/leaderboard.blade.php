@extends('layouts.admin')

@section('title', 'Sponsor Leaderboard')

@section('content')
<div
    x-data="{
        offcanvasOpen: false,
        selectedSponsorName: '',
        selectedReferrals: [],
        selectedFilter: '{{ $filter }}',
        openReferrals(name, referrals) {
            this.selectedSponsorName = name;
            this.selectedReferrals = referrals;
            this.offcanvasOpen = true;
        },
        openReferralsFromButton(el) {
            const name = el.dataset.sponsorName || 'Sponsor';
            let referrals = [];

            try {
                referrals = JSON.parse(el.dataset.referrals || '[]');
            } catch (e) {
                referrals = [];
            }

            this.openReferrals(name, referrals);
        }
    }"
    class="space-y-6"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-neutral-900">Sponsor Leaderboard</h2>
            <p class="text-sm text-neutral-600 mt-1">
                Ranked by most referred sponsors in: <span class="font-semibold">{{ $rangeLabel }}</span>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.sponsors.print.leaderboard', request()->query()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-800 hover:bg-neutral-50 transition order-2 sm:order-1">
                Print report
            </a>
            <a href="{{ route('admin.sponsors.index') }}" class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition order-1 sm:order-2">
                Back to sponsors
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-4 sm:p-6">
        <form method="GET" action="{{ route('admin.sponsors.leaderboard') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label for="filter" class="block text-sm font-medium text-neutral-700 mb-1">Show leaderboard by</label>
                    <select name="filter" id="filter" x-model="selectedFilter" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                        <option value="month">Month</option>
                        <option value="week">Week</option>
                        <option value="day">Day</option>
                        <option value="date">Custom Date Range</option>
                    </select>
                </div>
                <div>
                    <label for="per_page" class="block text-sm font-medium text-neutral-700 mb-1">Rows per page</label>
                    <select name="per_page" id="per_page" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                        @foreach([10, 20, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rounded-lg bg-neutral-50 border border-neutral-200 px-3 py-2 text-sm text-neutral-600 flex items-center">
                    <span>Current range: <span class="font-semibold text-neutral-800">{{ $rangeLabel }}</span></span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4" x-show="selectedFilter === 'month'">
                <div>
                    <label for="month" class="block text-sm font-medium text-neutral-700 mb-1">Select month</label>
                    <input type="month" name="month" id="month" value="{{ request('month', now()->format('Y-m')) }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4" x-show="selectedFilter === 'week'">
                <div>
                    <label for="week" class="block text-sm font-medium text-neutral-700 mb-1">Select week</label>
                    <input type="week" name="week" id="week" value="{{ request('week', now()->format('Y-\WW')) }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4" x-show="selectedFilter === 'day'">
                <div>
                    <label for="day" class="block text-sm font-medium text-neutral-700 mb-1">Select day</label>
                    <input type="date" name="day" id="day" value="{{ request('day', now()->toDateString()) }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4" x-show="selectedFilter === 'date'">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-neutral-700 mb-1">From date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from', $rangeStart->toDateString()) }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-1">To date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to', $rangeEnd->toDateString()) }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-light transition">Apply</button>
                <a href="{{ route('admin.sponsors.leaderboard') }}" class="inline-flex items-center rounded-lg border border-neutral-300 px-5 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition">Reset</a>
            </div>

            <div class="text-xs text-neutral-500">
                Tip: Choose one filter type (month/week/day/custom range), then apply.
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">Sponsor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">Filtered Referrals</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">Total Referrals</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-neutral-500">Balance</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-neutral-500">Pending (est.)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($sponsors as $index => $sponsor)
                    @php
                        $sponsorPhotoUrl = $sponsor->photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($sponsor->photo) : null;
                        $referralPayload = $sponsor->referrals->map(function ($referral) {
                            return [
                                'id' => $referral->id,
                                'name' => $referral->name,
                                'phone' => $referral->phone,
                                'photo_url' => $referral->photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($referral->photo) : null,
                                'affiliate_code' => $referral->affiliate_code,
                                'created_at' => optional($referral->created_at)->format('d M Y'),
                            ];
                        })->values();
                    @endphp
                    <tr class="hover:bg-neutral-50/80">
                        <td class="px-4 py-3 text-sm font-semibold text-neutral-800">
                            {{ (($sponsors->currentPage() - 1) * $sponsors->perPage()) + $index + 1 }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($sponsorPhotoUrl)
                                    <img src="{{ $sponsorPhotoUrl }}" alt="{{ $sponsor->name }}" class="w-10 h-10 rounded-full object-cover border border-neutral-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-neutral-200 flex items-center justify-center border border-neutral-200">
                                        <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold text-neutral-900">{{ $sponsor->name }}</div>
                                    <div class="text-xs text-neutral-500">Joined {{ $sponsor->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-neutral-700">{{ $sponsor->phone ?: 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-neutral-700">{{ $sponsor->affiliate_code ?: 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-primary">{{ $sponsor->filtered_referrals_count }}</td>
                        <td class="px-4 py-3 text-sm text-neutral-700">{{ $sponsor->referrals_count }}</td>
                        <td class="px-4 py-3 text-sm text-right tabular-nums font-medium text-neutral-900">৳{{ number_format($sponsor->balance ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right tabular-nums font-semibold text-violet-700">৳{{ number_format($sponsor->pending_purchase_commission_estimate ?? 0, 2) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="inline-flex rounded-md bg-primary/10 px-3 py-1.5 text-xs font-semibold text-primary hover:bg-primary/20 transition">
                                    View
                                </a>
                                <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="inline-flex rounded-md bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200 transition">
                                    Edit
                                </a>
                                <button
                                    type="button"
                                    data-sponsor-name="{{ $sponsor->name }}"
                                    data-referrals='@json($referralPayload)'
                                    @click="openReferralsFromButton($el)"
                                    class="inline-flex rounded-md bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200 transition"
                                >
                                    List
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-neutral-500">
                            No sponsor data found for the selected filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden divide-y divide-neutral-200">
            @forelse($sponsors as $index => $sponsor)
            @php
                $sponsorPhotoUrl = $sponsor->photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($sponsor->photo) : null;
                $referralPayload = $sponsor->referrals->map(function ($referral) {
                    return [
                        'id' => $referral->id,
                        'name' => $referral->name,
                        'phone' => $referral->phone,
                        'photo_url' => $referral->photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($referral->photo) : null,
                        'affiliate_code' => $referral->affiliate_code,
                        'created_at' => optional($referral->created_at)->format('d M Y'),
                    ];
                })->values();
                $rank = (($sponsors->currentPage() - 1) * $sponsors->perPage()) + $index + 1;
            @endphp
            <div class="p-4">
                <div class="flex items-start gap-3">
                    @if($sponsorPhotoUrl)
                        <img src="{{ $sponsorPhotoUrl }}" alt="{{ $sponsor->name }}" class="w-12 h-12 rounded-full object-cover border border-neutral-200 flex-shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-full bg-neutral-200 flex items-center justify-center border border-neutral-200 flex-shrink-0">
                            <svg class="w-6 h-6 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-neutral-900 truncate">{{ $sponsor->name }}</p>
                            <span class="inline-flex items-center rounded-md bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">#{{ $rank }}</span>
                        </div>
                        <p class="text-xs text-neutral-500 mt-0.5">Joined {{ $sponsor->created_at->format('d M Y') }}</p>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-1 mt-3 text-xs">
                            <p class="text-neutral-600">Phone: <span class="text-neutral-800">{{ $sponsor->phone ?: 'N/A' }}</span></p>
                            <p class="text-neutral-600">Code: <span class="text-neutral-800 font-mono">{{ $sponsor->affiliate_code ?: 'N/A' }}</span></p>
                            <p class="text-neutral-600">Filtered: <span class="text-primary font-semibold">{{ $sponsor->filtered_referrals_count }}</span></p>
                            <p class="text-neutral-600">Total: <span class="text-neutral-800 font-semibold">{{ $sponsor->referrals_count }}</span></p>
                            <p class="text-neutral-600">Balance: <span class="text-neutral-900 font-semibold tabular-nums">৳{{ number_format($sponsor->balance ?? 0, 2) }}</span></p>
                            <p class="text-neutral-600">Pending: <span class="text-violet-700 font-semibold tabular-nums">৳{{ number_format($sponsor->pending_purchase_commission_estimate ?? 0, 2) }}</span></p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-neutral-100">
                            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="inline-flex rounded-md bg-primary/10 px-3 py-1.5 text-xs font-semibold text-primary hover:bg-primary/20 transition">
                                View
                            </a>
                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="inline-flex rounded-md bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200 transition">
                                Edit
                            </a>
                            <button
                                type="button"
                                data-sponsor-name="{{ $sponsor->name }}"
                                data-referrals='@json($referralPayload)'
                                @click="openReferralsFromButton($el)"
                                class="inline-flex rounded-md bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200 transition"
                            >
                                List
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-sm text-neutral-500">
                No sponsor data found for the selected filter.
            </div>
            @endforelse
        </div>
    </div>

    @if($sponsors->hasPages())
    <div>
        {{ $sponsors->links() }}
    </div>
    @endif

    <div
        x-show="offcanvasOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-neutral-900/40"
        @click="offcanvasOpen = false"
        style="display: none;"
    ></div>

    <aside
        x-show="offcanvasOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 z-50 h-full w-full max-w-lg bg-white shadow-2xl border-l border-neutral-200"
        style="display: none;"
    >
        <div class="flex items-center justify-between border-b border-neutral-200 px-5 py-4">
            <div>
                <h3 class="text-lg font-bold text-neutral-900" x-text="selectedSponsorName"></h3>
                <p class="text-xs text-neutral-500">Listed sponsors under this sponsor</p>
            </div>
            <button type="button" class="rounded-md p-2 text-neutral-500 hover:bg-neutral-100 hover:text-neutral-700" @click="offcanvasOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="h-[calc(100%-72px)] overflow-y-auto p-5">
            <template x-if="selectedReferrals.length === 0">
                <p class="rounded-lg border border-dashed border-neutral-300 p-4 text-sm text-neutral-500">
                    No listed sponsors found.
                </p>
            </template>

            <div class="space-y-3" x-show="selectedReferrals.length > 0">
                <template x-for="referral in selectedReferrals" :key="referral.id">
                    <div class="rounded-lg border border-neutral-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <template x-if="referral.photo_url">
                                    <img :src="referral.photo_url" :alt="referral.name || 'Sponsor photo'" class="w-10 h-10 rounded-full object-cover border border-neutral-200">
                                </template>
                                <template x-if="!referral.photo_url">
                                    <div class="w-10 h-10 rounded-full bg-neutral-200 flex items-center justify-center border border-neutral-200">
                                        <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </template>
                                <div>
                                    <p class="text-sm font-semibold text-neutral-900" x-text="referral.name || 'Unnamed'"></p>
                                    <p class="text-xs text-neutral-600 mt-1">
                                        Phone: <span x-text="referral.phone || 'N/A'"></span>
                                    </p>
                                    <p class="text-xs text-neutral-600">
                                        Code: <span class="font-mono" x-text="referral.affiliate_code || 'N/A'"></span>
                                    </p>
                                    <p class="text-xs text-neutral-500 mt-1">
                                        Joined: <span x-text="referral.created_at || 'N/A'"></span>
                                    </p>
                                </div>
                            </div>
                            <a :href="`{{ url('/admin/sponsors') }}/${referral.id}`" class="inline-flex rounded-md bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary hover:bg-primary/20 transition">
                                View
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </aside>
</div>
@endsection
