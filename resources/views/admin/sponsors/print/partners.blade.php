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
    <p class="muted">Page {{ $sponsors->currentPage() }} of {{ $sponsors->lastPage() }} — {{ $sponsors->total() }} total</p>
</div>
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
            <td colspan="4" style="text-align:center">No partners on this page.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
