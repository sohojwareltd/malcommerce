@extends('layouts.admin')

@section('title', 'Purchase #' . $purchase->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.purchases.index', ['status' => $backStatus]) }}"
       class="text-sm font-semibold text-primary hover:underline inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to sponsor purchases
    </a>
    <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 mt-3">Purchase #{{ $purchase->id }}</h1>
    <p class="text-neutral-600 mt-1 text-sm">Full request details. Pending items can be accepted or canceled here.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-5 sm:p-6">
            <h2 class="text-sm font-semibold text-neutral-500 uppercase tracking-wide mb-4">Purchase</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-neutral-500">Status</dt>
                    <dd class="mt-1 font-semibold">
                        @if($purchase->status === 'pending')
                            <span class="text-amber-700">Pending</span>
                        @elseif($purchase->status === 'accepted')
                            <span class="text-green-700">Accepted</span>
                        @else
                            <span class="text-neutral-600">Canceled</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-neutral-500">Kind</dt>
                    <dd class="mt-1">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $purchase->kind === 'team' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $purchase->kind === 'team' ? 'Team purchase' : 'Own purchase' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-neutral-500">Declared amount (gross)</dt>
                    <dd class="mt-1 text-lg font-bold text-neutral-900">৳{{ number_format($purchase->amount, 2) }}</dd>
                    <p class="text-xs text-neutral-500 mt-1">On accept, the beneficiary’s balance gets commission on this amount only (level % or settings fallback).</p>
                </div>
                <div>
                    <dt class="text-neutral-500">Submitted</dt>
                    <dd class="mt-1 font-medium text-neutral-800">{{ $purchase->created_at->format('M d, Y H:i') }}</dd>
                </div>
                @if($purchase->processed_at)
                    <div>
                        <dt class="text-neutral-500">Processed</dt>
                        <dd class="mt-1 font-medium text-neutral-800">{{ $purchase->processed_at->format('M d, Y H:i') }}</dd>
                    </div>
                @endif
                @if($purchase->processedBy)
                    <div>
                        <dt class="text-neutral-500">Processed by</dt>
                        <dd class="mt-1 font-medium text-neutral-800">{{ $purchase->processedBy->name }}</dd>
                    </div>
                @endif
            </dl>
            <div class="mt-6 pt-6 border-t border-neutral-100">
                <dt class="text-sm text-neutral-500">Comment</dt>
                <dd class="mt-2 text-neutral-800 whitespace-pre-wrap">{{ $purchase->comment ?: '—' }}</dd>
            </div>
        </div>

        @if($purchase->earning)
            <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-neutral-500 uppercase tracking-wide mb-4">Linked earning</h2>
                <p class="text-sm text-neutral-700 mb-2">This purchase was credited via earning record #{{ $purchase->earning->id }}.</p>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-neutral-500">Type</dt>
                        <dd class="mt-0.5 font-medium">{{ $purchase->earning->earning_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Credited (commission)</dt>
                        <dd class="mt-0.5 font-medium">৳{{ number_format($purchase->earning->amount, 2) }}</dd>
                    </div>
                    @if(is_array($purchase->earning->meta))
                        @if(isset($purchase->earning->meta['purchase_gross_amount']))
                            <div>
                                <dt class="text-neutral-500">Gross declared</dt>
                                <dd class="mt-0.5 font-medium tabular-nums">৳{{ number_format((float) $purchase->earning->meta['purchase_gross_amount'], 2) }}</dd>
                            </div>
                        @endif
                        @if(isset($purchase->earning->meta['commission_percent']))
                            <div>
                                <dt class="text-neutral-500">Rate applied</dt>
                                <dd class="mt-0.5 font-medium tabular-nums">{{ number_format((float) $purchase->earning->meta['commission_percent'], 2) }}%</dd>
                            </div>
                        @endif
                    @endif
                    @if($purchase->earning->comment)
                        <div class="sm:col-span-2">
                            <dt class="text-neutral-500">Earning note</dt>
                            <dd class="mt-0.5 whitespace-pre-wrap">{{ $purchase->earning->comment }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-5 sm:p-6">
            <h2 class="text-sm font-semibold text-neutral-500 uppercase tracking-wide mb-4">Submitted by</h2>
            @php $sub = $purchase->submittedBy; @endphp
            <p class="font-semibold text-neutral-900">{{ $sub->name }}</p>
            <ul class="mt-3 space-y-2 text-sm text-neutral-700">
                <li><span class="text-neutral-500">Code:</span> <span class="font-mono">{{ $sub->affiliate_code ?: '—' }}</span></li>
                <li><span class="text-neutral-500">Phone:</span> {{ $sub->phone ?: '—' }}</li>
                <li><span class="text-neutral-500">Email:</span> {{ $sub->email ?: '—' }}</li>
            </ul>
            @can('sponsors.view')
                <a href="{{ route('admin.sponsors.show', $sub) }}" class="mt-4 inline-block text-sm font-semibold text-primary hover:underline">Open sponsor profile</a>
            @endcan
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-5 sm:p-6">
            <h2 class="text-sm font-semibold text-neutral-500 uppercase tracking-wide mb-4">Beneficiary (balance recipient)</h2>
            @php $ben = $purchase->beneficiary; @endphp
            <p class="font-semibold text-neutral-900">{{ $ben->name }}</p>
            <ul class="mt-3 space-y-2 text-sm text-neutral-700">
                <li><span class="text-neutral-500">Code:</span> <span class="font-mono">{{ $ben->affiliate_code ?: '—' }}</span></li>
                <li><span class="text-neutral-500">Phone:</span> {{ $ben->phone ?: '—' }}</li>
                <li><span class="text-neutral-500">Email:</span> {{ $ben->email ?: '—' }}</li>
            </ul>
            @can('sponsors.view')
                <a href="{{ route('admin.sponsors.show', $ben) }}" class="mt-4 inline-block text-sm font-semibold text-primary hover:underline">Open sponsor profile</a>
            @endcan
        </div>

        @if($purchase->isPending())
            <div class="bg-amber-50 rounded-xl border border-amber-200 shadow-sm p-5 sm:p-6">
                <h2 class="text-sm font-semibold text-amber-900 uppercase tracking-wide mb-4">Actions</h2>
                <p class="text-sm text-amber-900/80 mb-4">Accepting credits the beneficiary balance and creates the earning. Canceling cannot be undone.</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700">Accept purchase</button>
                    </form>
                    <form method="POST" action="{{ route('admin.purchases.update-status', $purchase) }}" class="flex-1" onsubmit="return confirm('Cancel this purchase?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="canceled">
                        <button type="submit" class="w-full px-4 py-2.5 rounded-lg border-2 border-red-300 text-red-700 text-sm font-semibold hover:bg-red-50">Cancel purchase</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
