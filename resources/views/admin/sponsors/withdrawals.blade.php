@extends('layouts.admin')

@section('title', 'Partner withdrawals — '.$sponsor->name)

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 text-sm">{{ session('success') }}</div>
@endif

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Withdrawals</h1>
        <p class="text-sm text-neutral-600 mt-1">
            {{ $sponsor->name }}
            @if($sponsor->phone)
                <span class="text-neutral-400"> · </span>{{ $sponsor->phone }}
            @endif
            <span class="font-mono text-neutral-500"> · {{ $sponsor->affiliate_code }}</span>
        </p>
        <p class="text-sm text-neutral-700 mt-2">
            Wallet balance after credits / deductions: <span class="font-semibold tabular-nums text-green-700">৳{{ number_format($sponsor->balance, 2) }}</span>
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="px-4 py-2 rounded-lg border border-neutral-300 text-neutral-700 text-sm font-semibold hover:bg-neutral-50 transition">
            ← Partner profile
        </a>
        <a href="{{ route('admin.sponsors.index') }}" class="px-4 py-2 rounded-lg bg-neutral-200 text-neutral-800 text-sm font-semibold hover:bg-neutral-300 transition">
            All partners
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md border border-neutral-200 overflow-hidden lg:col-span-2">
        <div class="border-b border-neutral-200 px-5 py-4 bg-neutral-50/90">
            <h2 class="text-lg font-bold text-neutral-900">Request history</h2>
            <p class="text-sm text-neutral-600 mt-0.5">Same rules as partner self-service: balance is deducted when the request is created.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold text-neutral-600">ID</th>
                        <th class="px-4 py-2.5 text-right font-semibold text-neutral-600">Amount</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-neutral-600">Status</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-neutral-600">Requested</th>
                        <th class="px-4 py-2.5 text-right font-semibold text-neutral-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($withdrawals as $w)
                        <tr class="hover:bg-neutral-50/80">
                            <td class="px-4 py-2.5 font-mono text-neutral-700">#{{ $w->id }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold tabular-nums">৳{{ number_format($w->amount, 2) }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold
                                    {{ $w->status === 'pending' ? 'bg-amber-100 text-amber-800' :
                                       ($w->status === 'approved' ? 'bg-green-100 text-green-800' :
                                       ($w->status === 'cancelled' ? 'bg-neutral-200 text-neutral-700' :
                                       ($w->status === 'inquiry' ? 'bg-violet-100 text-violet-800' : 'bg-blue-100 text-blue-800'))) }}">
                                    {{ ucfirst($w->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-neutral-600 whitespace-nowrap">{{ $w->requested_at?->format('M d, Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                @can('update', $w)
                                    <a href="{{ route('admin.withdrawals.show', $w) }}" class="text-primary text-xs font-semibold hover:underline">Manage</a>
                                @else
                                    <a href="{{ route('admin.withdrawals.show', $w) }}" class="text-neutral-600 text-xs font-semibold hover:underline">View</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-neutral-500">No withdrawal requests for this partner yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
            <div class="border-t border-neutral-100 px-4 py-3">{{ $withdrawals->links() }}</div>
        @endif
    </div>

    @can('withdrawals.create')
    @php
        $hasSavedMethods = is_array($methods) && count($methods) > 0;
        $selectedMethodKey = old('method_key');
        if ($selectedMethodKey === null || ($hasSavedMethods && ! isset($methods[$selectedMethodKey]))) {
            if ($defaultKey && $hasSavedMethods && isset($methods[$defaultKey])) {
                $selectedMethodKey = $defaultKey;
            } elseif ($hasSavedMethods) {
                $selectedMethodKey = array_key_first($methods);
            } else {
                $selectedMethodKey = null;
            }
        }
    @endphp
    <div class="space-y-6 lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md border border-emerald-200 p-6">
            <h2 class="text-xl font-bold text-neutral-900 mb-1">New withdrawal request</h2>
            <p class="text-sm text-neutral-600 mb-6">Balance is deducted when the request is created. Pick an existing MFS account, record a <strong>cash</strong> payout, or add a new mobile wallet and submit in one step.</p>

            @if($hasSavedMethods)
            <div class="rounded-lg border border-neutral-200 bg-neutral-50/80 p-5 mb-6">
                <h3 class="text-sm font-semibold text-neutral-900 mb-3">Use saved payout method</h3>
                <form action="{{ route('admin.sponsors.withdrawals.store', $sponsor) }}" method="POST" class="space-y-4 max-w-lg">
                    @csrf
                    <input type="hidden" name="payout_option" value="saved">
                    <div>
                        <label for="method_key_saved" class="block text-sm font-medium text-neutral-700 mb-1">Payout method</label>
                        <select name="method_key" id="method_key_saved" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm bg-white">
                            @foreach($methods as $key => $method)
                                @php
                                    $number = $method['number'] ?? '';
                                    $label = $method['label'] ?? (strtoupper($method['provider'] ?? 'mfs'));
                                @endphp
                                <option value="{{ $key }}" {{ (string) $selectedMethodKey === (string) $key ? 'selected' : '' }}>
                                    {{ $label }} · {{ strtoupper($method['provider'] ?? '') }} · {{ $number ?: '—' }}
                                </option>
                            @endforeach
                        </select>
                        @error('method_key')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="amount_saved" class="block text-sm font-medium text-neutral-700 mb-1">Amount (৳)</label>
                        <input type="number" name="amount" id="amount_saved" step="0.01" min="1" value="{{ old('payout_option') === 'saved' ? old('amount') : '' }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums">
                        @error('amount') @if(old('payout_option') === 'saved')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@endif @enderror
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition">Submit withdrawal</button>
                </form>
            </div>
            @endif

            <div class="rounded-lg border border-amber-200 bg-amber-50/40 p-5 mb-6">
                <h3 class="text-sm font-semibold text-neutral-900 mb-1">Cash payout</h3>
                <p class="text-xs text-neutral-600 mb-3">Use when you pay the partner in cash (office pickup, hand delivery, etc.). No MFS account required.</p>
                <form action="{{ route('admin.sponsors.withdrawals.store', $sponsor) }}" method="POST" class="space-y-4 max-w-lg">
                    @csrf
                    <input type="hidden" name="payout_option" value="cash">
                    <div>
                        <label for="amount_cash" class="block text-sm font-medium text-neutral-700 mb-1">Amount (৳)</label>
                        <input type="number" name="amount" id="amount_cash" step="0.01" min="1" value="{{ old('payout_option') === 'cash' ? old('amount') : '' }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums">
                        @error('amount')
                            @if(old('payout_option') === 'cash')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @endif
                        @enderror
                    </div>
                    <div>
                        <label for="cash_note" class="block text-sm font-medium text-neutral-700 mb-1">Internal note (optional)</label>
                        <input type="text" name="cash_note" id="cash_note" maxlength="500" value="{{ old('cash_note') }}" placeholder="e.g. handed at Dhaka office, receipt #…" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm">
                        @error('cash_note')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-amber-700 text-white rounded-lg font-semibold hover:bg-amber-800 transition">Record cash withdrawal</button>
                </form>
            </div>

            <div class="rounded-lg border border-sky-200 bg-sky-50/40 p-5">
                <h3 class="text-sm font-semibold text-neutral-900 mb-1">Add new MFS account &amp; withdraw</h3>
                <p class="text-xs text-neutral-600 mb-3">Creates a payout method on this partner’s profile (set as default) and submits the withdrawal in one go.</p>
                <form action="{{ route('admin.sponsors.withdrawals.store', $sponsor) }}" method="POST" class="space-y-4 max-w-xl">
                    @csrf
                    <input type="hidden" name="payout_option" value="new_mfs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="provider_new" class="block text-sm font-medium text-neutral-700 mb-1">Provider</label>
                            <select name="provider" id="provider_new" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm bg-white">
                                <option value="">— Select —</option>
                                @foreach(['bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket'] as $val => $lab)
                                    <option value="{{ $val }}" {{ old('payout_option') === 'new_mfs' && old('provider') === $val ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                            @error('provider')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="number_new" class="block text-sm font-medium text-neutral-700 mb-1">Mobile number</label>
                            <input type="text" name="number" id="number_new" inputmode="numeric" autocomplete="off" value="{{ old('payout_option') === 'new_mfs' ? old('number') : '' }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm">
                            @error('number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="account_type_new" class="block text-sm font-medium text-neutral-700 mb-1">Account type</label>
                            <select name="account_type" id="account_type_new" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm bg-white">
                                <option value="personal" {{ old('payout_option') === 'new_mfs' && old('account_type') === 'personal' ? 'selected' : '' }}>Personal</option>
                                <option value="agent" {{ old('payout_option') === 'new_mfs' && old('account_type') === 'agent' ? 'selected' : '' }}>Agent</option>
                            </select>
                            @error('account_type')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="holder_name_new" class="block text-sm font-medium text-neutral-700 mb-1">Account holder name</label>
                            <input type="text" name="holder_name" id="holder_name_new" value="{{ old('payout_option') === 'new_mfs' ? old('holder_name') : '' }}" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm">
                            @error('holder_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="new_method_label" class="block text-sm font-medium text-neutral-700 mb-1">Label (optional)</label>
                        <input type="text" name="new_method_label" id="new_method_label" value="{{ old('payout_option') === 'new_mfs' ? old('new_method_label') : '' }}" placeholder="e.g. Primary bKash" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm">
                        @error('new_method_label')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="amount_new" class="block text-sm font-medium text-neutral-700 mb-1">Amount (৳)</label>
                        <input type="number" name="amount" id="amount_new" step="0.01" min="1" value="{{ old('payout_option') === 'new_mfs' ? old('amount') : '' }}" required class="w-full max-w-xs px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums">
                        @error('amount') @if(old('payout_option') === 'new_mfs')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@endif @enderror
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-sky-700 text-white rounded-lg font-semibold hover:bg-sky-800 transition">Save method &amp; submit withdrawal</button>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
