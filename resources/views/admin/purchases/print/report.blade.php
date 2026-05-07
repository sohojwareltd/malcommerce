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
    <p class="muted">{{ $purchases->count() }} row(s)</p>
</div>
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
