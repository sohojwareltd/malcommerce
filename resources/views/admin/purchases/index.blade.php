@extends('layouts.admin')

@section('title', 'Sponsor Purchases')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Sponsor purchases</h1>
        <p class="text-neutral-600 mt-1 text-sm">Approve or cancel purchase requests. On accept, the beneficiary’s balance increases by commission on the declared amount (their sponsor level %, or the settings fallback if they have no level).</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2 shrink-0">
        <a href="{{ route('admin.purchases.print', request()->query()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg border border-emerald-700 bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-sm">
            Print report
        </a>
        <a href="{{ route('admin.purchases.create') }}" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-light transition">
            Record purchase
        </a>
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-4">
    @foreach(['pending' => 'Pending', 'accepted' => 'Accepted', 'canceled' => 'Canceled', 'all' => 'All'] as $key => $label)
        <a href="{{ route('admin.purchases.index', array_merge(request()->except('page'), ['status' => $key])) }}"
           class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ ($status === $key) ? 'bg-primary text-white' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200' }}">
            {{ $label }}
            @if($key !== 'all' && isset($counts[$key]))
                <span class="opacity-80">({{ $counts[$key] }})</span>
            @endif
        </a>
    @endforeach
</div>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-4 sm:p-6 mb-6" x-data="{ period: '{{ $periodType }}' }">
    <form method="GET" action="{{ route('admin.purchases.index') }}" class="space-y-4">
        <input type="hidden" name="status" value="{{ $status }}">
        <div>
            <span class="block text-sm font-medium text-neutral-700 mb-2">Date range for list &amp; print</span>
            <div class="flex flex-wrap gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-neutral-800 cursor-pointer">
                    <input type="radio" name="period" value="all" x-model="period" class="rounded-full border-neutral-300 text-primary focus:ring-primary">
                    All dates
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-neutral-800 cursor-pointer">
                    <input type="radio" name="period" value="month" x-model="period" class="rounded-full border-neutral-300 text-primary focus:ring-primary">
                    By month
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-neutral-800 cursor-pointer">
                    <input type="radio" name="period" value="range" x-model="period" class="rounded-full border-neutral-300 text-primary focus:ring-primary">
                    Custom dates
                </label>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div x-show="period === 'month'" x-cloak class="sm:col-span-1">
                <label for="filter_month" class="block text-sm font-medium text-neutral-700 mb-1">Month</label>
                <input type="month" name="month" id="filter_month" value="{{ request('month', now()->format('Y-m')) }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
            </div>
            <div x-show="period === 'range'" x-cloak class="sm:col-span-1">
                <label for="date_from" class="block text-sm font-medium text-neutral-700 mb-1">From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
            </div>
            <div x-show="period === 'range'" x-cloak class="sm:col-span-1">
                <label for="date_to" class="block text-sm font-medium text-neutral-700 mb-1">To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-neutral-300 text-sm focus:ring-primary focus:border-primary">
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-light transition">Apply</button>
                <a href="{{ route('admin.purchases.index', ['status' => $status]) }}" class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-5 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-100 transition">Clear dates</a>
            </div>
        </div>
        <p class="text-xs text-neutral-500">Current filter: <span class="font-semibold text-neutral-700">{{ $rangeLabel }}</span>@if($status !== 'all') · Status: <span class="font-semibold text-neutral-700">{{ ucfirst($status) }}</span>@else · Status: All@endif</p>
    </form>
</div>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Date</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Kind</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Submitted by</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Beneficiary</th>
                    <th class="px-4 py-3 text-right font-semibold text-neutral-600">Amount</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Comment</th>
                    <th class="px-4 py-3 text-left font-semibold text-neutral-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-neutral-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($purchases as $purchase)
                    <tr class="hover:bg-neutral-50/80">
                        <td class="px-4 py-3 whitespace-nowrap text-neutral-600">{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $purchase->kind === 'team' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $purchase->kind === 'team' ? 'Team' : 'Own' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($purchase->submittedBy->photo)
                                    <img src="{{ Storage::disk('public')->url($purchase->submittedBy->photo) }}" alt="" class="w-9 h-9 rounded-full object-cover border border-neutral-200 flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-neutral-200 flex items-center justify-center border border-neutral-200 flex-shrink-0" aria-hidden="true">
                                        <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-medium text-neutral-900 truncate">{{ $purchase->submittedBy->name }}</div>
                                    <div class="text-xs text-neutral-500 font-mono truncate">{{ $purchase->submittedBy->affiliate_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($purchase->beneficiary->photo)
                                    <img src="{{ Storage::disk('public')->url($purchase->beneficiary->photo) }}" alt="" class="w-9 h-9 rounded-full object-cover border border-neutral-200 flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-neutral-200 flex items-center justify-center border border-neutral-200 flex-shrink-0" aria-hidden="true">
                                        <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-medium text-neutral-900 truncate">{{ $purchase->beneficiary->name }}</div>
                                    <div class="text-xs text-neutral-500 font-mono truncate">{{ $purchase->beneficiary->affiliate_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-neutral-900">৳{{ number_format($purchase->amount, 2) }}</td>
                        <td class="px-4 py-3 text-neutral-600 max-w-xs truncate" title="{{ $purchase->comment }}">{{ $purchase->comment ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($purchase->status === 'pending')
                                <span class="text-amber-700 font-semibold text-xs">Pending</span>
                            @elseif($purchase->status === 'accepted')
                                <span class="text-green-700 font-semibold text-xs">Accepted</span>
                            @else
                                <span class="text-neutral-500 font-semibold text-xs">Canceled</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.purchases.show', ['purchase' => $purchase, 'from_status' => $status]) }}"
                               class="text-primary hover:underline font-semibold text-xs mr-3">View</a>
                            @if($purchase->status === 'pending')
                                <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="text-green-700 hover:underline font-semibold text-xs mr-2">Accept</button>
                                </form>
                                @can('withdrawals.create')
                                <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline" onsubmit="return confirm('Accept and immediately withdraw the beneficiary’s commission as cash?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <input type="hidden" name="withdraw_after_accept" value="1">
                                    <button type="submit" class="text-teal-700 hover:underline font-semibold text-xs mr-2">Accept &amp; cash withdraw</button>
                                </form>
                                @endcan
                                <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline" onsubmit="return confirm('Cancel this purchase?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="canceled">
                                    <button type="submit" class="text-red-600 hover:underline font-semibold text-xs">Cancel</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.purchases.destroy', $purchase) }}" class="inline" onsubmit="return confirm('{{ $purchase->status === 'accepted' ? 'Delete this accepted purchase and rollback commission earnings/balances?' : 'Delete this purchase request?' }}');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="from_status" value="{{ $status }}">
                                <button type="submit" class="text-red-700 hover:underline font-semibold text-xs ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-neutral-500">No purchases in this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($purchases as $purchase)
            <div class="p-4 hover:bg-neutral-50/80 transition-colors">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-neutral-500">{{ $purchase->created_at->format('M d, Y H:i') }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $purchase->kind === 'team' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $purchase->kind === 'team' ? 'Team' : 'Own' }}
                            </span>
                            @if($purchase->status === 'pending')
                                <span class="text-amber-700 font-semibold text-xs">Pending</span>
                            @elseif($purchase->status === 'accepted')
                                <span class="text-green-700 font-semibold text-xs">Accepted</span>
                            @else
                                <span class="text-neutral-500 font-semibold text-xs">Canceled</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-neutral-900 tabular-nums flex-shrink-0">৳{{ number_format($purchase->amount, 2) }}</p>
                </div>
                <div class="grid grid-cols-1 gap-3 text-sm mb-3">
                    <div>
                        <span class="text-neutral-500 text-xs uppercase tracking-wide">Submitted by</span>
                        <div class="flex items-center gap-2 mt-1">
                            @if($purchase->submittedBy->photo)
                                <img src="{{ Storage::disk('public')->url($purchase->submittedBy->photo) }}" alt="" class="w-10 h-10 rounded-full object-cover border border-neutral-200 flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-neutral-200 flex items-center justify-center border border-neutral-200 flex-shrink-0">
                                    <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-neutral-900">{{ $purchase->submittedBy->name }}</p>
                                <p class="text-xs text-neutral-500 font-mono">{{ $purchase->submittedBy->affiliate_code }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="text-neutral-500 text-xs uppercase tracking-wide">Beneficiary</span>
                        <div class="flex items-center gap-2 mt-1">
                            @if($purchase->beneficiary->photo)
                                <img src="{{ Storage::disk('public')->url($purchase->beneficiary->photo) }}" alt="" class="w-10 h-10 rounded-full object-cover border border-neutral-200 flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-neutral-200 flex items-center justify-center border border-neutral-200 flex-shrink-0">
                                    <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-neutral-900">{{ $purchase->beneficiary->name }}</p>
                                <p class="text-xs text-neutral-500 font-mono">{{ $purchase->beneficiary->affiliate_code }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="text-neutral-500 text-xs uppercase tracking-wide">Comment</span>
                        <p class="text-neutral-700 break-words">{{ $purchase->comment ?: '—' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 pt-3 border-t border-neutral-200">
                    <a href="{{ route('admin.purchases.show', ['purchase' => $purchase, 'from_status' => $status]) }}"
                       class="text-primary hover:underline font-semibold text-sm">View</a>
                    @if($purchase->status === 'pending')
                        <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="text-green-700 hover:underline font-semibold text-sm">Accept</button>
                        </form>
                        @can('withdrawals.create')
                        <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline" onsubmit="return confirm('Accept and immediately withdraw the beneficiary’s commission as cash?');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="accepted">
                            <input type="hidden" name="withdraw_after_accept" value="1">
                            <button type="submit" class="text-teal-700 hover:underline font-semibold text-sm">Accept &amp; cash withdraw</button>
                        </form>
                        @endcan
                        <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline" onsubmit="return confirm('Cancel this purchase?');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="canceled">
                            <button type="submit" class="text-red-600 hover:underline font-semibold text-sm">Cancel</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.purchases.destroy', $purchase) }}" class="inline" onsubmit="return confirm('{{ $purchase->status === 'accepted' ? 'Delete this accepted purchase and rollback commission earnings/balances?' : 'Delete this purchase request?' }}');">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="from_status" value="{{ $status }}">
                        <button type="submit" class="text-red-700 hover:underline font-semibold text-sm">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-neutral-500 text-sm">No purchases in this filter.</div>
        @endforelse
    </div>

    @if($purchases->hasPages())
        <div class="px-4 py-3 border-t border-neutral-100">{{ $purchases->links() }}</div>
    @endif
</div>
@endsection
