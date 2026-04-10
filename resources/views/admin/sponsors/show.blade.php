@extends('layouts.admin')

@section('title', 'Sponsor Details')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
$pendingPurchasesTotal = $purchaseSubmitted['pending_count'] + $purchaseAsBeneficiary['pending_count'];
@endphp

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-800 text-sm">{{ session('error') }}</div>
@endif
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-3xl font-bold">Sponsor Details</h1>
        <p class="text-neutral-600 mt-1">{{ $sponsor->name }} ({{ $sponsor->affiliate_code }})</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @can('sponsors.update')
        <a href="{{ route('admin.sponsors.edit', $sponsor) }}?promote=level" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-semibold">
            Promote level
        </a>
        <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition">
            Edit Sponsor
        </a>
        @endcan
        <a href="{{ route('admin.sponsors.index') }}" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
            ← Back to Sponsors
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content (tabbed) -->
    <div
        class="lg:col-span-2"
        x-data="{
            tab: 'overview',
            setTab(name) {
                this.tab = name;
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + name);
                }
            },
            syncHash() {
                const h = window.location.hash.slice(1);
                if (['overview', 'income', 'purchases', 'network', 'metrics'].includes(h)) {
                    this.tab = h;
                }
            }
        }"
        x-init="syncHash(); window.addEventListener('hashchange', () => syncHash())"
        x-cloak
    >
        <nav class="flex flex-wrap gap-2 mb-6 pb-4 border-b border-neutral-200" aria-label="Sponsor detail sections">
            <button
                type="button"
                @click="setTab('overview')"
                :class="tab === 'overview' ? 'bg-primary text-white shadow-sm ring-2 ring-primary/20' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200'"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition"
            >Overview</button>
            <button
                type="button"
                @click="setTab('income')"
                :class="tab === 'income' ? 'bg-primary text-white shadow-sm ring-2 ring-primary/20' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200'"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition"
            >Income & balance</button>
            <button
                type="button"
                @click="setTab('purchases')"
                :class="tab === 'purchases' ? 'bg-primary text-white shadow-sm ring-2 ring-primary/20' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200'"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold transition"
            >
                Purchases
                @if($pendingPurchasesTotal > 0)
                    <span class="ml-2 inline-flex min-w-[1.25rem] justify-center rounded-full bg-amber-500 px-1.5 py-0.5 text-xs font-bold text-white">{{ $pendingPurchasesTotal }}</span>
                @endif
            </button>
            <button
                type="button"
                @click="setTab('network')"
                :class="tab === 'network' ? 'bg-primary text-white shadow-sm ring-2 ring-primary/20' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200'"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition"
            >Referrals & orders</button>
            <button
                type="button"
                @click="setTab('metrics')"
                :class="tab === 'metrics' ? 'bg-primary text-white shadow-sm ring-2 ring-primary/20' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200'"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition"
            >Metrics</button>
        </nav>

        <!-- Tab: Overview -->
        <div class="space-y-6" x-show="tab === 'overview'">
            <div class="bg-white rounded-lg shadow-md p-5 border border-neutral-100">
                <h2 class="text-lg font-bold text-neutral-900 mb-1">At a glance</h2>
                <p class="text-sm text-neutral-600 mb-4">Order and referral activity linked to this partner.</p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-lg bg-neutral-50 p-4 border border-neutral-100">
                        <p class="text-sm font-medium text-neutral-500">Total orders</p>
                        <p class="text-2xl font-bold mt-1">{{ $stats['total_orders'] }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4 border border-neutral-100">
                        <p class="text-sm font-medium text-neutral-500">Pending orders</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending_orders'] }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4 border border-neutral-100">
                        <p class="text-sm font-medium text-neutral-500">Total revenue</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">৳{{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4 border border-neutral-100">
                        <p class="text-sm font-medium text-neutral-500">Referrals</p>
                        <p class="text-2xl font-bold mt-1">{{ $stats['total_referrals'] }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4 border border-neutral-100 col-span-2 sm:col-span-1 lg:col-span-1">
                        <p class="text-sm font-medium text-neutral-500">Delivered</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['delivered_orders'] }}</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="button" @click="setTab('income')" class="text-sm font-semibold text-primary hover:underline">View income & balance →</button>
                    <button type="button" @click="setTab('purchases')" class="text-sm font-semibold text-primary hover:underline">View purchases →</button>
                    <button type="button" @click="setTab('network')" class="text-sm font-semibold text-primary hover:underline">View referrals & orders →</button>
                </div>
            </div>
        </div>

        <!-- Tab: Income & balance -->
        <div class="space-y-6" x-show="tab === 'income'">
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-xl shadow-lg p-6 text-white">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-white/90">Income & balance</h2>
                        <p class="text-sm text-white/60 mt-1">What this sponsor has been credited, what they hold now, and withdrawals.</p>
                    </div>
                    <a href="{{ route('admin.purchases.index', ['status' => 'pending']) }}" class="shrink-0 inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm font-semibold text-white border border-white/20 transition">
                        Review purchase queue
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="rounded-xl bg-white/10 border border-white/10 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-emerald-300/90">Current balance</p>
                        <p class="text-3xl font-bold mt-2 tabular-nums">৳{{ number_format($sponsor->balance, 2) }}</p>
                        <p class="text-xs text-white/50 mt-2">Available in their wallet after credits and withdrawal deductions.</p>
                    </div>
                    <div class="rounded-xl bg-white/10 border border-white/10 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-sky-300/90">Lifetime income credited</p>
                        <p class="text-3xl font-bold mt-2 tabular-nums text-sky-100">৳{{ number_format($lifetimeEarnings, 2) }}</p>
                        <p class="text-xs text-white/50 mt-2">Sum of all earning records for this sponsor.</p>
                    </div>
                    <div class="rounded-xl bg-white/10 border border-white/10 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-amber-200/90">Withdrawals paid out</p>
                        <p class="text-3xl font-bold mt-2 tabular-nums text-amber-50">৳{{ number_format($withdrawalSummary['approved_total'], 2) }}</p>
                        <p class="text-xs text-white/50 mt-2">Approved withdrawal total (money sent or marked complete).</p>
                    </div>
                    <div class="rounded-xl bg-white/10 border border-white/10 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-rose-200/90">Withdrawals in progress</p>
                        <p class="text-3xl font-bold mt-2 tabular-nums text-rose-50">৳{{ number_format($withdrawalSummary['in_queue_total'], 2) }}</p>
                        <p class="text-xs text-white/50 mt-2">
                            {{ $withdrawalSummary['in_queue_count'] }} open request(s) — pending, processing, or inquiry.
                        </p>
                    </div>
                </div>
                @if($withdrawalSummary['cancelled_total'] > 0)
                    <p class="text-xs text-white/45 mt-4">Cancelled withdrawals returned to balance: ৳{{ number_format($withdrawalSummary['cancelled_total'], 2) }} (historical).</p>
                @endif
            </div>

            @can('sponsors.update')
            <div class="bg-white rounded-lg shadow-md p-6 border-2 border-emerald-200">
                <h2 class="text-xl font-bold text-neutral-900 mb-1">Edit current balance</h2>
                <p class="text-sm text-neutral-600 mb-4">Set an exact wallet balance for this sponsor.</p>
                <form action="{{ route('admin.sponsors.balance.update', $sponsor) }}" method="POST" class="space-y-4 max-w-md">
                    @csrf
                    <div>
                        <label for="set-balance" class="block text-sm font-medium text-neutral-700 mb-1">New balance (৳)</label>
                        <input
                            type="number"
                            name="balance"
                            id="set-balance"
                            step="0.01"
                            min="0"
                            value="{{ old('balance', number_format((float) $sponsor->balance, 2, '.', '')) }}"
                            class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums"
                            required
                        >
                        @error('balance')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        Update balance
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-2 border-emerald-200">
                <h2 class="text-xl font-bold text-neutral-900 mb-1">Add income directly</h2>
                <p class="text-sm text-neutral-600 mb-4">Credits this sponsor’s balance and writes a row in the <strong>sponsor incomes</strong> log (plus a matching earning for their statement). Pick a preset category <strong>or</strong> type your own (custom overrides the list if both are set).</p>
                <form action="{{ route('admin.sponsors.income.store', $sponsor) }}" method="POST" class="space-y-4 max-w-2xl">
                    @csrf
                    <div>
                        <label for="income-amount" class="block text-sm font-medium text-neutral-700 mb-1">Amount (৳) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="income-amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full max-w-xs px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums">
                        @error('amount')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-3 rounded-lg border border-neutral-200 bg-neutral-50/80 p-4">
                        <p class="text-sm font-medium text-neutral-800">Category <span class="text-red-500">*</span></p>
                        <div>
                            <label for="income-category-preset" class="block text-xs font-medium text-neutral-600 mb-1">Select from list</label>
                            <select name="category_preset" id="income-category-preset" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary bg-white text-sm">
                                <option value="">— Choose a preset —</option>
                                @foreach($sponsorIncomeCategorySuggestions as $hint)
                                    <option value="{{ $hint }}" {{ old('category_preset') === $hint ? 'selected' : '' }}>{{ $hint }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative flex items-center gap-3">
                            <div class="flex-1 border-t border-neutral-200"></div>
                            <span class="text-xs font-medium text-neutral-500 uppercase tracking-wide">or</span>
                            <div class="flex-1 border-t border-neutral-200"></div>
                        </div>
                        <div>
                            <label for="income-category-custom" class="block text-xs font-medium text-neutral-600 mb-1">Enter manually</label>
                            <input type="text" name="category_custom" id="income-category-custom" value="{{ old('category_custom') }}" maxlength="255" placeholder="Any label (e.g. Q1 special adjustment)" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm">
                            <p class="text-xs text-neutral-500 mt-1">If this field is filled, it is used as the category instead of the dropdown.</p>
                        </div>
                        @error('category_preset')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('category_custom')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="income-notes" class="block text-sm font-medium text-neutral-700 mb-1">Notes (optional)</label>
                        <textarea name="notes" id="income-notes" rows="2" maxlength="2000" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="Internal context, reference ID, special case…">{{ old('notes') }}</textarea>
                        @error('notes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition">Add income &amp; update balance</button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 overflow-hidden">
                <h2 class="text-xl font-bold mb-1">Manual income log</h2>
                <p class="text-sm text-neutral-600 mb-4">Admin-added credits from the table <code class="text-xs bg-neutral-100 px-1 rounded">sponsor_incomes</code>.</p>
                @if($manualIncomes->isEmpty())
                    <p class="text-sm text-neutral-500">No manual income entries yet.</p>
                @else
                    <div class="overflow-x-auto -mx-6 px-6">
                        <table class="min-w-full divide-y divide-neutral-200 text-sm">
                            <thead class="bg-neutral-50">
                                <tr>
                                    <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Date</th>
                                    <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Category</th>
                                    <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Notes</th>
                                    <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">By</th>
                                    <th class="px-3 py-2.5 text-right font-semibold text-neutral-600">Amount</th>
                                    <th class="px-3 py-2.5 text-right font-semibold text-neutral-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach($manualIncomes as $row)
                                <tr class="hover:bg-neutral-50/80">
                                    <td class="px-3 py-2.5 whitespace-nowrap text-neutral-600">{{ $row->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-3 py-2.5 font-medium text-neutral-900">{{ $row->category }}</td>
                                    <td class="px-3 py-2.5 text-neutral-600 max-w-xs">{{ $row->notes ? Str::limit($row->notes, 120) : '—' }}</td>
                                    <td class="px-3 py-2.5 text-neutral-600">{{ $row->creator?->name ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-right font-semibold text-green-700 tabular-nums">+৳{{ number_format($row->amount, 2) }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        <form action="{{ route('admin.sponsors.income.destroy', [$sponsor, $row]) }}" method="POST" onsubmit="return confirm('Delete this income entry? This will also reduce the sponsor balance.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @endcan

            @if($earningsByType->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-1">Income by source</h2>
                <p class="text-sm text-neutral-600 mb-4">Breakdown of all amounts ever credited through earnings.</p>
                <div class="space-y-3">
                    @foreach($earningsByType as $type => $total)
                        @php
                            $label = $earningTypeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
                            $pct = $lifetimeEarnings > 0 ? round(($total / $lifetimeEarnings) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-neutral-800">{{ $label }}</span>
                                <span class="text-neutral-600 tabular-nums">৳{{ number_format($total, 2) }} <span class="text-neutral-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
                                <div class="h-full rounded-full bg-primary" style="width: {{ $pct > 0 ? $pct : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($recentEarnings->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md p-6 overflow-hidden">
                <h2 class="text-xl font-bold mb-1">Recent income entries</h2>
                <p class="text-sm text-neutral-600 mb-4">Latest earning records credited to this sponsor.</p>
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full divide-y divide-neutral-200 text-sm">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Date</th>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Type</th>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Detail</th>
                                <th class="px-3 py-2.5 text-right font-semibold text-neutral-600">Amount</th>
                                <th class="px-3 py-2.5 text-right font-semibold text-neutral-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($recentEarnings as $e)
                                <tr class="hover:bg-neutral-50/80">
                                    <td class="px-3 py-2.5 whitespace-nowrap text-neutral-600">{{ $e->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-3 py-2.5">
                                        <span class="capitalize text-neutral-800 font-medium">{{ str_replace('_', ' ', $e->earning_type) }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-neutral-600 max-w-md">
                                        @if($e->order)
                                            <a href="{{ route('admin.orders.show', $e->order) }}" class="text-primary font-medium hover:underline">Order #{{ $e->order->order_number }}</a>
                                            @if($e->comment)<span class="text-neutral-500"> — {{ Str::limit($e->comment, 80) }}</span>@endif
                                        @elseif($e->referral)
                                            <span>Referral: {{ $e->referral->name }}</span>
                                            @if($e->comment)<span class="text-neutral-500"> — {{ Str::limit($e->comment, 60) }}</span>@endif
                                        @elseif($e->earning_type === 'manual_income' && is_array($e->meta) && ! empty($e->meta['category']))
                                            <span class="font-medium text-neutral-800">{{ $e->meta['category'] }}</span>
                                            @if(! empty($e->meta['notes']))<span class="text-neutral-500"> — {{ Str::limit((string) $e->meta['notes'], 70) }}</span>@endif
                                        @else
                                            {{ $e->comment ?: '—' }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-semibold text-green-700 tabular-nums">+৳{{ number_format($e->amount, 2) }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        <form action="{{ route('admin.sponsors.earnings.destroy', [$sponsor, $e]) }}" method="POST" onsubmit="return confirm('Delete this earning? This will also reduce the sponsor balance.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-md p-6 border border-dashed border-neutral-200">
                <p class="text-sm text-neutral-600">No earning entries recorded for this sponsor yet.</p>
            </div>
            @endif
        </div>

        <!-- Tab: Purchases -->
        <div class="space-y-6" x-show="tab === 'purchases'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-amber-500">
                    <h3 class="text-lg font-bold text-neutral-900">Purchase requests they submitted</h3>
                    <p class="text-sm text-neutral-600 mt-1">Amounts they asked admin to credit (own or team).</p>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Awaiting review</dt>
                            <dd class="font-semibold text-amber-800">{{ $purchaseSubmitted['pending_count'] }} · ৳{{ number_format($purchaseSubmitted['pending_amount'], 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Accepted</dt>
                            <dd class="font-semibold text-green-700">{{ $purchaseSubmitted['accepted_count'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Canceled</dt>
                            <dd class="font-semibold text-neutral-600">{{ $purchaseSubmitted['canceled_count'] }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-emerald-500">
                    <h3 class="text-lg font-bold text-neutral-900">Purchase credits for them</h3>
                    <p class="text-sm text-neutral-600 mt-1">Requests where this sponsor is the beneficiary (balance recipient).</p>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Awaiting review</dt>
                            <dd class="font-semibold text-amber-800">{{ $purchaseAsBeneficiary['pending_count'] }} · ৳{{ number_format($purchaseAsBeneficiary['pending_amount'], 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Accepted</dt>
                            <dd class="font-semibold text-green-700">{{ $purchaseAsBeneficiary['accepted_count'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Canceled</dt>
                            <dd class="font-semibold text-neutral-600">{{ $purchaseAsBeneficiary['canceled_count'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($recentPurchases->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md p-6 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <div>
                        <h2 class="text-xl font-bold">Recent purchase activity</h2>
                        <p class="text-sm text-neutral-600">Involving this sponsor as submitter or beneficiary.</p>
                    </div>
                    <a href="{{ route('admin.purchases.index', ['status' => 'all']) }}" class="text-sm font-semibold text-primary hover:underline">All purchases</a>
                </div>
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full divide-y divide-neutral-200 text-sm">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Date</th>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Role</th>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Kind</th>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Other party</th>
                                <th class="px-3 py-2.5 text-right font-semibold text-neutral-600">Amount</th>
                                <th class="px-3 py-2.5 text-left font-semibold text-neutral-600">Status</th>
                                <th class="px-3 py-2.5 text-right font-semibold text-neutral-600"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($recentPurchases as $p)
                                @php
                                    $isSubmitter = $p->submitted_by_sponsor_id === $sponsor->id;
                                    $roleLabel = $isSubmitter ? 'Submitted' : 'Beneficiary';
                                    $other = $isSubmitter ? $p->beneficiary : $p->submittedBy;
                                @endphp
                                <tr class="hover:bg-neutral-50/80">
                                    <td class="px-3 py-2.5 whitespace-nowrap text-neutral-600">{{ $p->created_at->format('M d, Y') }}</td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $isSubmitter ? 'bg-violet-100 text-violet-800' : 'bg-teal-100 text-teal-800' }}">{{ $roleLabel }}</span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $p->kind === 'team' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $p->kind === 'team' ? 'Team' : 'Own' }}</span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <span class="font-medium text-neutral-900">{{ $other->name }}</span>
                                        <span class="block text-xs text-neutral-500 font-mono">{{ $other->affiliate_code }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-semibold tabular-nums">৳{{ number_format($p->amount, 2) }}</td>
                                    <td class="px-3 py-2.5">
                                        @if($p->status === 'pending')
                                            <span class="text-amber-700 font-semibold text-xs">Pending</span>
                                        @elseif($p->status === 'accepted')
                                            <span class="text-green-700 font-semibold text-xs">Accepted</span>
                                        @else
                                            <span class="text-neutral-500 font-semibold text-xs">Canceled</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <a href="{{ route('admin.purchases.show', ['purchase' => $p, 'from_status' => 'all']) }}" class="text-primary font-semibold text-xs hover:underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-md p-6 border border-dashed border-neutral-200">
                <h2 class="text-lg font-bold text-neutral-800">Purchase requests</h2>
                <p class="text-sm text-neutral-600 mt-1">No purchase records yet for this sponsor.</p>
            </div>
            @endif
        </div>

        <!-- Tab: Referrals & orders -->
        <div class="space-y-6" x-show="tab === 'network'">
            @if($sponsor->referrals->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Referrals ({{ $sponsor->referrals->count() }})</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Photo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Phone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Partner Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Orders</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @foreach($sponsor->referrals as $referral)
                            <tr class="hover:bg-neutral-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($referral->photo)
                                        <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-10 h-10 rounded object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded bg-neutral-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900">
                                    <a href="{{ route('admin.sponsors.show', $referral) }}" class="text-primary hover:underline">{{ $referral->name }}</a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $referral->phone ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500 font-mono">{{ $referral->affiliate_code }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $referral->orders_count }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $referral->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Referrals</h2>
                <p class="text-neutral-500 text-center py-8">No referrals yet</p>
            </div>
            @endif

            @if($sponsor->orders->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Recent orders</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order #</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @foreach($sponsor->orders->take(10) as $order)
                            <tr class="hover:bg-neutral-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $order->product->name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">৳{{ number_format($order->total_price, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($order->status === 'delivered' ? 'bg-green-100 text-green-800' :
                                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                           'bg-blue-100 text-blue-800')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Tab: Metrics -->
        <div class="space-y-6" x-show="tab === 'metrics'" x-cloak>
            <div class="bg-white rounded-lg shadow-md p-5 border border-neutral-100">
                <h2 class="text-lg font-bold text-neutral-900 mb-1">Performance & consistency</h2>
                <p class="text-sm text-neutral-600 mb-4">Accepted purchases you submitted (by processed month), withdrawals (approved), and a 0–100 consistency score from month-to-month purchase amounts.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="rounded-lg bg-sky-50 p-4 border border-sky-100">
                        <p class="text-sm font-medium text-sky-800">Consistency score</p>
                        <p class="text-2xl font-bold tabular-nums mt-1">
                            @if($sponsorMetrics['consistency'] !== null)
                                {{ $sponsorMetrics['consistency'] }}
                            @else
                                <span class="text-neutral-400 text-lg font-normal">Need 2+ active months</span>
                            @endif
                        </p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4 border border-neutral-100">
                        <p class="text-sm font-medium text-neutral-600">Accepted purchases (30d)</p>
                        <p class="text-xl font-bold tabular-nums mt-1">৳{{ number_format($sponsorMetrics['purchase_totals']['day_30'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4 border border-neutral-100">
                        <p class="text-sm font-medium text-neutral-600">Accepted purchases (this month)</p>
                        <p class="text-xl font-bold tabular-nums mt-1">৳{{ number_format($sponsorMetrics['purchase_totals']['month_current'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-4 border border-emerald-100">
                        <p class="text-sm font-medium text-emerald-800">Peer sample (avg score)</p>
                        <p class="text-xl font-bold tabular-nums mt-1">
                            @if($sponsorMetrics['peer_sample_avg_consistency'] !== null)
                                {{ $sponsorMetrics['peer_sample_avg_consistency'] }}
                            @else
                                <span class="text-neutral-400 text-base font-normal">—</span>
                            @endif
                        </p>
                        <p class="text-xs text-emerald-700/80 mt-1">Random sample of other sponsors; cached ~1h.</p>
                    </div>
                </div>
                @if($sponsor->sponsor)
                <div class="rounded-lg border border-neutral-200 p-4 mb-6 bg-neutral-50/80">
                    <p class="text-sm font-semibold text-neutral-800">vs direct referrer</p>
                    <p class="text-sm text-neutral-600 mt-1">
                        Referrer:
                        <a href="{{ route('admin.sponsors.show', $sponsor->sponsor) }}#metrics" class="text-primary font-medium hover:underline">{{ $sponsor->sponsor->name }}</a>
                        — consistency:
                        @if($sponsorMetrics['referrer_consistency'] !== null)
                            <span class="font-mono font-semibold">{{ $sponsorMetrics['referrer_consistency'] }}</span>
                        @else
                            <span class="text-neutral-400">n/a</span>
                        @endif
                    </p>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md p-5 border border-neutral-100 overflow-x-auto">
                    <h3 class="text-base font-bold text-neutral-900 mb-3">Accepted purchases by month</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-neutral-500 border-b border-neutral-200">
                                <th class="py-2 pr-4">Month</th>
                                <th class="py-2 pr-4">Amount</th>
                                <th class="py-2">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($sponsorMetrics['purchases']['months'] as $idx => $ym)
                            <tr>
                                <td class="py-2 pr-4 font-mono text-xs">{{ $ym }}</td>
                                <td class="py-2 pr-4 tabular-nums">৳{{ number_format($sponsorMetrics['purchases']['amounts'][$idx], 2) }}</td>
                                <td class="py-2 tabular-nums">{{ $sponsorMetrics['purchases']['counts'][$idx] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bg-white rounded-lg shadow-md p-5 border border-neutral-100 overflow-x-auto">
                    <h3 class="text-base font-bold text-neutral-900 mb-3">Approved withdrawals by month</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-neutral-500 border-b border-neutral-200">
                                <th class="py-2 pr-4">Month</th>
                                <th class="py-2 pr-4">Amount</th>
                                <th class="py-2">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($sponsorMetrics['withdrawals']['months'] as $idx => $ym)
                            <tr>
                                <td class="py-2 pr-4 font-mono text-xs">{{ $ym }}</td>
                                <td class="py-2 pr-4 tabular-nums">৳{{ number_format($sponsorMetrics['withdrawals']['amounts'][$idx], 2) }}</td>
                                <td class="py-2 tabular-nums">{{ $sponsorMetrics['withdrawals']['counts'][$idx] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Sponsor Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Sponsor Information</h2>

            <!-- Photo -->
            <div class="mb-4 flex justify-center">
                @if($sponsor->photo)
                    <img src="{{ Storage::disk('public')->url($sponsor->photo) }}" alt="{{ $sponsor->name }}" class="w-24 h-24 rounded-full object-cover border-2 border-neutral-200">
                @else
                    <div class="w-24 h-24 rounded-full bg-neutral-200 flex items-center justify-center border-2 border-neutral-300">
                        <svg class="w-12 h-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Name</dt>
                    <dd class="mt-1 text-sm font-semibold">{{ $sponsor->name }}</dd>
                </div>

                @if($sponsor->email)
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Email</dt>
                    <dd class="mt-1 text-sm">{{ $sponsor->email }}</dd>
                </div>
                @endif

                @if($sponsor->phone)
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Phone</dt>
                    <dd class="mt-1 text-sm">{{ $sponsor->phone }}</dd>
                </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-neutral-500">Current balance</dt>
                    <dd class="mt-1 text-sm font-semibold text-green-700 tabular-nums">৳{{ number_format($sponsor->balance, 2) }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-neutral-500">Partner Code</dt>
                    <dd class="mt-1 text-sm font-mono font-semibold">{{ $sponsor->affiliate_code }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-neutral-500">Sponsor level</dt>
                    <dd class="mt-1 text-sm">
                        @if($sponsor->sponsorLevel)
                            <span class="font-semibold">{{ $sponsor->sponsorLevel->name }}</span>
                            <span class="text-neutral-500 text-xs ml-1">rank {{ $sponsor->sponsorLevel->rank }}, {{ number_format($sponsor->sponsorLevel->commission_percent, 2) }}%</span>
                        @else
                            <span class="text-neutral-500">None (legacy referral)</span>
                        @endif
                    </dd>
                    @can('sponsors.update')
                    <p class="mt-2">
                        <a href="{{ route('admin.sponsors.edit', $sponsor) }}?promote=level" class="text-sm font-medium text-amber-700 hover:text-amber-900">Promote / change level</a>
                    </p>
                    @endcan
                </div>

                <div>
                    <dt class="text-sm font-medium text-neutral-500">Referrer</dt>
                    <dd class="mt-1 text-sm">
                        @if($sponsor->sponsor)
                            @if($sponsor->sponsor->trashed())
                                <span class="font-medium text-neutral-700">{{ $sponsor->sponsor->name }}</span>
                                <span class="text-neutral-500 font-mono text-xs ml-1">({{ $sponsor->sponsor->affiliate_code }})</span>
                                <span class="text-neutral-400 text-xs ml-1">(deleted)</span>
                            @else
                                <a href="{{ route('admin.sponsors.show', $sponsor->sponsor) }}" class="text-primary hover:underline font-medium">{{ $sponsor->sponsor->name }}</a>
                                <span class="text-neutral-500 font-mono text-xs ml-1">({{ $sponsor->sponsor->affiliate_code }})</span>
                            @endif
                        @else
                            <span class="text-neutral-500">—</span>
                        @endif
                    </dd>
                </div>

                @if($sponsor->address)
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Address</dt>
                    <dd class="mt-1 text-sm whitespace-pre-line">{{ $sponsor->address }}</dd>
                </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-neutral-500">Joined</dt>
                    <dd class="mt-1 text-sm">{{ $sponsor->created_at->format('M d, Y h:i A') }}</dd>
                </div>

                @if($sponsor->createdFromOrder)
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Created From Order</dt>
                    <dd class="mt-1 text-sm">
                        <a href="{{ route('admin.orders.show', $sponsor->createdFromOrder) }}" class="text-primary hover:underline font-medium">
                            Order #{{ $sponsor->createdFromOrder->order_number }}
                        </a>
                        @if($sponsor->createdFromOrder->product)
                            <span class="text-neutral-500 mx-1">•</span>
                            <span class="text-neutral-800">Product: {{ $sponsor->createdFromOrder->product->name }}</span>
                        @endif
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        @if($sponsor->sponsorLevelHistories->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-3">Level history</h2>
            <ul class="space-y-3 text-sm">
                @foreach($sponsor->sponsorLevelHistories as $h)
                <li class="border-b border-neutral-100 pb-3 last:border-0 last:pb-0">
                    <span class="text-neutral-500">{{ $h->created_at->format('M d, Y H:i') }}</span>
                    @if($h->changedBy)
                        <span class="text-neutral-400"> · by {{ $h->changedBy->name }}</span>
                    @endif
                    <p class="mt-1">
                        {{ $h->fromLevel?->name ?? 'None' }}
                        <span class="text-neutral-400">→</span>
                        {{ $h->toLevel?->name ?? 'None' }}
                    </p>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- General Partner Link -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">General Partner Link</h2>
            <p class="text-sm text-neutral-600 mb-3">Share this link to earn commissions on all products:</p>
            <div class="flex gap-2">
                <input
                    type="text"
                    value="{{ url('/') }}?ref={{ $sponsor->affiliate_code }}"
                    readonly
                    class="flex-1 px-3 py-2 text-xs border border-neutral-300 rounded bg-neutral-50 font-mono"
                    id="general-partner-link"
                >
                <button
                    onclick="copyPartnerLink('general-partner-link')"
                    class="px-3 py-2 bg-primary text-white text-xs rounded hover:bg-primary-light transition whitespace-nowrap"
                >
                    Copy
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="space-y-2">
                @can('sponsors.update')
                <a href="{{ route('admin.sponsors.edit', $sponsor) }}?promote=level" class="block w-full text-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-semibold">
                    Promote level
                </a>
                <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition">
                    Edit Sponsor
                </a>
                @endcan
                <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sponsor? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Delete Sponsor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyPartnerLink(inputId) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        navigator.clipboard.writeText(input.value).then(function() {
            const button = input.nextElementSibling;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.add('bg-green-600');
            button.classList.remove('bg-primary', 'hover:bg-primary-light');
            setTimeout(function() {
                button.textContent = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-primary', 'hover:bg-primary-light');
            }, 2000);
        }).catch(function(err) {
            alert('Failed to copy link');
        });
    }
</script>
@endpush
@endsection
