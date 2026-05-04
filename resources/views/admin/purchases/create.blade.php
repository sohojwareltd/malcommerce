@extends('layouts.admin')

@section('title', 'Record sponsor purchase')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">Record purchase</h1>
        <p class="text-neutral-600 mt-1 text-sm">Create a pending purchase on behalf of partners. You can approve immediately and optionally withdraw the beneficiary’s commission as a cash payout.</p>
    </div>
    <a href="{{ route('admin.purchases.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-primary hover:underline shrink-0">← Back to purchases</a>
</div>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-5 sm:p-6 max-w-3xl">
    <form action="{{ route('admin.purchases.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Kind <span class="text-red-500">*</span></label>
            <div class="flex flex-wrap gap-4">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="kind" value="own" class="text-primary focus:ring-primary" {{ old('kind', 'own') === 'own' ? 'checked' : '' }} required>
                    <span class="text-sm text-neutral-800">Own (submitter = beneficiary)</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="kind" value="team" class="text-primary focus:ring-primary" {{ old('kind') === 'team' ? 'checked' : '' }} required>
                    <span class="text-sm text-neutral-800">Team (beneficiary must be submitter’s referral)</span>
                </label>
            </div>
            @error('kind')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <x-admin.sponsor-referrer-picker
            :referrers="$referrerOptions"
            name="submitted_by_sponsor_id"
            :selected="old('submitted_by_sponsor_id')"
            label="Submitted by (partner)"
            hint="Partner who is filing this request."
        />

        <x-admin.sponsor-referrer-picker
            :referrers="$referrerOptions"
            name="beneficiary_user_id"
            :selected="old('beneficiary_user_id')"
            label="Beneficiary (balance recipient)"
            hint="For own purchases, pick the same partner as submitted by. For team, pick their direct referral."
        />

        <div>
            <label for="amount" class="block text-sm font-medium text-neutral-700 mb-2">Declared amount (৳) <span class="text-red-500">*</span></label>
            <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full max-w-xs px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary tabular-nums">
            @error('amount')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="comment" class="block text-sm font-medium text-neutral-700 mb-2">Comment</label>
            <textarea name="comment" id="comment" rows="3" maxlength="2000" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary text-sm" placeholder="Optional context for reviewers">{{ old('comment') }}</textarea>
            @error('comment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-lg border border-neutral-200 bg-neutral-50/80 p-4 space-y-3">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="approve_immediately" value="1" class="mt-1 rounded border-neutral-300 text-primary focus:ring-primary" {{ old('approve_immediately') ? 'checked' : '' }}>
                <span>
                    <span class="text-sm font-semibold text-neutral-900">Approve immediately</span>
                    <span class="block text-xs text-neutral-600 mt-0.5">Credits commissions now (same as accepting from the queue). Leave unchecked to leave pending.</span>
                </span>
            </label>
            @can('withdrawals.create')
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="withdraw_after_accept" value="1" class="mt-1 rounded border-neutral-300 text-primary focus:ring-primary" {{ old('withdraw_after_accept') ? 'checked' : '' }}>
                <span>
                    <span class="text-sm font-semibold text-neutral-900">Withdraw beneficiary commission (cash)</span>
                    <span class="block text-xs text-neutral-600 mt-0.5">After approval, creates a cash withdrawal for the beneficiary’s credited amount only. Requires “Approve immediately”.</span>
                </span>
            </label>
            @endcan
            @error('withdraw_after_accept')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white font-semibold hover:bg-primary-light transition">Save purchase</button>
            <a href="{{ route('admin.purchases.index', ['status' => 'pending']) }}" class="px-5 py-2.5 rounded-lg border border-neutral-300 text-neutral-700 font-semibold hover:bg-neutral-50 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
