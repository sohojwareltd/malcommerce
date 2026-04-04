@extends('layouts.admin')

@section('title', 'Sponsor Purchases')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Sponsor purchases</h1>
        <p class="text-neutral-600 mt-1 text-sm">Approve or cancel purchase requests. On accept, the beneficiary’s balance increases by commission on the declared amount (their sponsor level %, or the settings fallback if they have no level).</p>
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-6">
    @foreach(['pending' => 'Pending', 'accepted' => 'Accepted', 'canceled' => 'Canceled', 'all' => 'All'] as $key => $label)
        <a href="{{ route('admin.purchases.index', ['status' => $key]) }}"
           class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ ($status === $key) ? 'bg-primary text-white' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200' }}">
            {{ $label }}
            @if($key !== 'all' && isset($counts[$key]))
                <span class="opacity-80">({{ $counts[$key] }})</span>
            @endif
        </a>
    @endforeach
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
                            <div class="font-medium text-neutral-900">{{ $purchase->submittedBy->name }}</div>
                            <div class="text-xs text-neutral-500 font-mono">{{ $purchase->submittedBy->affiliate_code }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-neutral-900">{{ $purchase->beneficiary->name }}</div>
                            <div class="text-xs text-neutral-500 font-mono">{{ $purchase->beneficiary->affiliate_code }}</div>
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
                                <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline" onsubmit="return confirm('Cancel this purchase?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="canceled">
                                    <button type="submit" class="text-red-600 hover:underline font-semibold text-xs">Cancel</button>
                                </form>
                            @endif
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
                        <p class="font-medium text-neutral-900">{{ $purchase->submittedBy->name }}</p>
                        <p class="text-xs text-neutral-500 font-mono">{{ $purchase->submittedBy->affiliate_code }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-500 text-xs uppercase tracking-wide">Beneficiary</span>
                        <p class="font-medium text-neutral-900">{{ $purchase->beneficiary->name }}</p>
                        <p class="text-xs text-neutral-500 font-mono">{{ $purchase->beneficiary->affiliate_code }}</p>
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
                        <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="inline" onsubmit="return confirm('Cancel this purchase?');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="canceled">
                            <button type="submit" class="text-red-600 hover:underline font-semibold text-sm">Cancel</button>
                        </form>
                    @endif
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
