@extends('admin.sponsors.print.layout')

@section('title', $reportTitle)

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<h1>{{ $reportTitle }}</h1>
<div class="meta">
    <p>Generated {{ now()->format('M d, Y g:i A') }}</p>
    @if($search)
        <p>Search: &quot;{{ $search }}&quot;</p>
    @endif
    <p class="muted">Only partners with wallet balance greater than ৳0. {{ $sponsors->count() }} row(s) in this report.</p>
</div>

<table class="summary">
    <caption>Summary (positive balance only; same filters as list)</caption>
    <tbody>
        <tr>
            <th scope="row">Partner count</th>
            <td class="num tabular-nums">{{ number_format($printSummary['partner_count']) }}</td>
        </tr>
        <tr>
            <th scope="row">Total wallet balance</th>
            <td class="num tabular-nums">৳{{ number_format($printSummary['total_balance'], 2) }}</td>
        </tr>
        <tr>
            <th scope="row">Purchase income (unwithdrawn, est.) <span class="muted">(purchase share of current wallet)</span></th>
            <td class="num tabular-nums">৳{{ number_format($printSummary['purchase_income_unwithdrawn'], 2) }}</td>
        </tr>
        <tr>
            <th scope="row">Order count <span class="muted">(non-cancelled)</span></th>
            <td class="num tabular-nums">{{ number_format($printSummary['order_count']) }}</td>
        </tr>
        <tr>
            <th scope="row">Direct referrals <span class="muted">(active partners they referred)</span></th>
            <td class="num tabular-nums">{{ number_format($printSummary['referral_count']) }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Phone</th>
            <th class="num">Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sponsors as $sponsor)
        <tr>
            <td>
                @if($sponsor->photo)
                    <img class="photo" src="{{ Storage::disk('public')->url($sponsor->photo) }}" alt="">
                @else
                    <span class="muted">—</span>
                @endif
            </td>
            <td>{{ $sponsor->name }}</td>
            <td>{{ $sponsor->phone ?? 'N/A' }}</td>
            <td class="num tabular-nums">৳{{ number_format($sponsor->balance ?? 0, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align:center">No partners in this report.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
