@php
    $institute = $student->institute;
@endphp

<div class="doc-header">
    @if($institute->logo_url)
        <img src="{{ $institute->logo_url }}" alt="" class="doc-logo">
    @else
        <div class="doc-logo-fallback">{{ strtoupper(substr($institute->name, 0, 1)) }}</div>
    @endif
    <div class="doc-header-text">
        @if($institute->approval_text)
            <div class="doc-approval">{{ $institute->approval_text }}</div>
        @else
            <div class="doc-approval">Approved By Govt. Of The People's Republic Of Bangladesh</div>
        @endif
        <div class="doc-institute-name">{{ $institute->name }}</div>
        @if($institute->name_bn)
            <div class="doc-institute-name-bn">{{ $institute->name_bn }}</div>
        @endif
        <div class="doc-contact">
            @if($institute->address) Address: {{ $institute->address }}<br>@endif
            @if($institute->email){{ $institute->email }}@endif
            @if($institute->email && $institute->website) | @endif
            @if($institute->website){{ $institute->website }}@endif
        </div>
    </div>
</div>
