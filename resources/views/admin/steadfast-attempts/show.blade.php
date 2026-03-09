@extends('layouts.admin')

@section('title', 'Steadfast Attempt #' . $attempt->id)

@section('content')
<div class="mb-4 sm:mb-6">
    <a href="{{ route('admin.steadfast-attempts.index') }}" class="text-primary hover:text-primary-light text-sm font-medium flex items-center gap-1 mb-2">
        ← Back to Steadfast Attempts
    </a>
    <h1 class="text-2xl sm:text-3xl font-bold">Steadfast Attempt #{{ $attempt->id }}</h1>
    <p class="text-neutral-600 mt-1 text-sm">{{ $attempt->created_at->format('Y-m-d H:i:s') }}</p>
</div>

<div class="space-y-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Summary</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm text-neutral-500">Type</dt>
                <dd class="font-medium">{{ $attempt->type }}{{ $attempt->notification_type ? ' (' . $attempt->notification_type . ')' : '' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-neutral-500">Result</dt>
                <dd>
                    @if($attempt->success)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Success</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Failed</span>
                    @endif
                    @if($attempt->http_status)
                        <span class="text-neutral-500 ml-1">HTTP {{ $attempt->http_status }}</span>
                    @endif
                </dd>
            </div>
            @if($attempt->order)
            <div>
                <dt class="text-sm text-neutral-500">Order</dt>
                <dd><a href="{{ route('admin.orders.show', $attempt->order) }}" class="text-primary hover:underline font-medium">#{{ $attempt->order->order_number }}</a></dd>
            </div>
            @endif
            @if($attempt->ip_address)
            <div>
                <dt class="text-sm text-neutral-500">IP Address</dt>
                <dd class="font-mono text-sm">{{ $attempt->ip_address }}</dd>
            </div>
            @endif
            @if($attempt->error_message)
            <div class="sm:col-span-2">
                <dt class="text-sm text-neutral-500">Error Message</dt>
                <dd class="text-red-700 font-medium">{{ $attempt->error_message }}</dd>
            </div>
            @endif
        </dl>
    </div>

    @if($attempt->request_payload && count($attempt->request_payload) > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Request Payload</h2>
        <pre class="bg-neutral-50 p-4 rounded-lg text-sm overflow-x-auto max-h-64 overflow-y-auto"><code>{{ json_encode($attempt->request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
    </div>
    @endif

    @if($attempt->response_payload && count($attempt->response_payload) > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Response Payload</h2>
        <pre class="bg-neutral-50 p-4 rounded-lg text-sm overflow-x-auto max-h-64 overflow-y-auto"><code>{{ json_encode($attempt->response_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
    </div>
    @endif
</div>
@endsection
