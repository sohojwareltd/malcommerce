@extends('admin.sponsors.print.layout')

@section('title', 'Sponsor purchases report')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<h1>Sponsor purchases</h1>
<div class="meta">
    <p>Status: {{ $statusLabel }}</p>
    <p>Period: {{ $rangeLabel }}</p>
    <p>Generated {{ now()->format('M d, Y g:i A') }}</p>
</div>

<table class="summary">
    <caption>Summary <span class="muted">(same filters as this report)</span></caption>
    <tbody>
        <tr>
            <th scope="row">Purchases in report</th>
            <td class="num tabular-nums">{{ number_format($printSummary['purchase_count']) }}</td>
        </tr>
        <tr>
            <th scope="row">Total declared amount <span class="muted">(sum of Amount column)</span></th>
            <td class="num tabular-nums">৳{{ number_format($printSummary['total_amount'], 2) }}</td>
        </tr>
        @if($status === 'all')
            <tr>
                <th scope="row">Pending <span class="muted">(count · amount)</span></th>
                <td class="num tabular-nums">{{ number_format($printSummary['pending_count']) }} · ৳{{ number_format($printSummary['pending_amount'], 2) }}</td>
            </tr>
            <tr>
                <th scope="row">Accepted <span class="muted">(count · amount)</span></th>
                <td class="num tabular-nums">{{ number_format($printSummary['accepted_count']) }} · ৳{{ number_format($printSummary['accepted_amount'], 2) }}</td>
            </tr>
            <tr>
                <th scope="row">Canceled <span class="muted">(count · amount)</span></th>
                <td class="num tabular-nums">{{ number_format($printSummary['canceled_count']) }} · ৳{{ number_format($printSummary['canceled_amount'], 2) }}</td>
            </tr>
        @endif
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Kind</th>
            <th>Submitted by</th>
            <th>Beneficiary</th>
            <th class="num">Amount</th>
            <th>Comment</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchases as $purchase)
        <tr>
            <td class="tabular-nums">{{ $purchase->created_at->format('M d, Y H:i') }}</td>
            <td>{{ $purchase->kind === 'team' ? 'Team' : 'Own' }}</td>
            <td>
                <div style="display:flex;align-items:center;gap:8px">
                    @if($purchase->submittedBy->photo ?? null)
                        <img class="photo" src="{{ Storage::disk('public')->url($purchase->submittedBy->photo) }}" alt="">
                    @else
                        <span class="muted">—</span>
                    @endif
                    <span>{{ $purchase->submittedBy->name }} <span class="muted font-mono">({{ $purchase->submittedBy->affiliate_code }})</span></span>
                </div>
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:8px">
                    @if($purchase->beneficiary->photo ?? null)
                        <img class="photo" src="{{ Storage::disk('public')->url($purchase->beneficiary->photo) }}" alt="">
                    @else
                        <span class="muted">—</span>
                    @endif
                    <span>{{ $purchase->beneficiary->name }} <span class="muted font-mono">({{ $purchase->beneficiary->affiliate_code }})</span></span>
                </div>
            </td>
            <td class="num tabular-nums">৳{{ number_format($purchase->amount, 2) }}</td>
            <td>{{ $purchase->comment ?: '—' }}</td>
            <td>{{ ucfirst($purchase->status) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center">No purchases for this report.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
