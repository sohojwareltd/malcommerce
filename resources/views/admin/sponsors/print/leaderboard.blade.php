@extends('admin.sponsors.print.layout')

@section('title', 'Sponsor leaderboard')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<h1>Sponsor leaderboard</h1>
<div class="meta">
    <p>Period: {{ $rangeLabel }}</p>
    <p>Generated {{ now()->format('M d, Y g:i A') }}</p>
    <p class="muted">Table: page {{ $sponsors->currentPage() }} of {{ $sponsors->lastPage() }} ({{ $sponsors->total() }} rows)</p>
</div>

<table class="summary">
    <caption>Summary (all active partners &amp; network)</caption>
    <tbody>
        <tr>
            <th scope="row">Active partner accounts</th>
            <td class="num tabular-nums">{{ number_format($printSummary['sponsor_count']) }}</td>
        </tr>
        <tr>
            <th scope="row">Combined wallet balance</th>
            <td class="num tabular-nums">৳{{ number_format($printSummary['total_balance'], 2) }}</td>
        </tr>
        <tr>
            <th scope="row">New referred partners in period</th>
            <td class="num tabular-nums">{{ number_format($printSummary['referrals_in_period']) }}</td>
        </tr>
        <tr>
            <th scope="row">Referred partners (all time)</th>
            <td class="num tabular-nums">{{ number_format($printSummary['referrals_all_time']) }}</td>
        </tr>
        <tr>
            <th scope="row">Total partner order revenue <span class="muted">(non-cancelled)</span></th>
            <td class="num tabular-nums">৳{{ number_format($printSummary['total_revenue'], 2) }}</td>
        </tr>
        <tr>
            <th scope="row">Partner order count <span class="muted">(non-cancelled)</span></th>
            <td class="num tabular-nums">{{ number_format($printSummary['order_count']) }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Code</th>
            <th class="num">In period</th>
            <th class="num">Total ref.</th>
            <th class="num">Balance</th>
            <th class="num">Pending</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sponsors as $index => $sponsor)
        @php
            $rowPhoto = $sponsor->photo ? Storage::disk('public')->url($sponsor->photo) : null;
            $rank = (($sponsors->currentPage() - 1) * $sponsors->perPage()) + $index + 1;
        @endphp
        <tr>
            <td>{{ $rank }}</td>
            <td>
                @if($rowPhoto)
                    <img class="photo" src="{{ $rowPhoto }}" alt="">
                @else
                    <span class="muted">—</span>
                @endif
            </td>
            <td>{{ $sponsor->name }}</td>
            <td>{{ $sponsor->phone ?: 'N/A' }}</td>
            <td style="font-family: ui-monospace, monospace">{{ $sponsor->affiliate_code ?: 'N/A' }}</td>
            <td class="num tabular-nums">{{ $sponsor->filtered_referrals_count }}</td>
            <td class="num tabular-nums">{{ $sponsor->referrals_count }}</td>
            <td class="num tabular-nums">৳{{ number_format($sponsor->balance ?? 0, 2) }}</td>
            <td class="num tabular-nums">৳{{ number_format($sponsor->pending_purchase_commission_estimate ?? 0, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center">No sponsor data for this report.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
