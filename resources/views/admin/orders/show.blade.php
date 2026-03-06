@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Order Details</h1>
        <p class="text-neutral-600 mt-1">Order #{{ $order->order_number }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.orders.edit', $order) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition font-semibold">
            Edit Order
        </a>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
            ← Back to Orders
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Order Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Order Information</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Order Number</dt>
                    <dd class="mt-1 text-sm font-semibold">{{ $order->order_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Order Date</dt>
                    <dd class="mt-1 text-sm">{{ $order->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Product</dt>
                    <dd class="mt-1 text-sm font-semibold">{{ $order->product->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Quantity</dt>
                    <dd class="mt-1 text-sm">{{ $order->quantity }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Unit Price</dt>
                    <dd class="mt-1 text-sm">৳{{ number_format($order->unit_price, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Delivery Charge</dt>
                    <dd class="mt-1 text-sm">৳{{ number_format($order->delivery_charge ?? 0, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Total Price</dt>
                    <dd class="mt-1 text-sm font-bold text-green-600">৳{{ number_format($order->total_price, 2) }}</dd>
                </div>
            </dl>
        </div>

        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Customer Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Name</dt>
                    <dd class="mt-1 text-sm font-semibold">{{ $order->customer_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Phone</dt>
                    <dd class="mt-1 text-sm">{{ $order->customer_phone }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Address</dt>
                    <dd class="mt-1 text-sm whitespace-pre-line">{{ $order->address }}</dd>
                </div>
            </dl>
        </div>

        <!-- Sponsor Information -->
        @if($order->sponsor)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Sponsor/Affiliate</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Name</dt>
                    <dd class="mt-1 text-sm font-semibold">{{ $order->sponsor->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Email</dt>
                    <dd class="mt-1 text-sm">{{ $order->sponsor->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Affiliate Code</dt>
                    <dd class="mt-1 text-sm font-mono">{{ $order->sponsor->affiliate_code }}</dd>
                </div>
            </dl>
        </div>
        @endif

        @if($order->notes)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Notes</h2>
            <p class="text-sm text-neutral-700 whitespace-pre-wrap">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Steadfast Courier / Parcel -->
        @if($order->product && !$order->product->is_digital)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Steadfast Courier</h2>
            @if($order->steadfast_consignment_id)
                <dl class="space-y-2">
                    <div>
                        <dt class="text-sm font-medium text-neutral-500">Consignment ID</dt>
                        <dd class="text-sm font-mono">{{ $order->steadfast_consignment_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-neutral-500">Tracking Code</dt>
                        <dd class="text-sm font-mono font-semibold">{{ $order->steadfast_tracking_code }}</dd>
                    </div>
                    @if($order->steadfast_delivery_status)
                    <div>
                        <dt class="text-sm font-medium text-neutral-500">Delivery Status</dt>
                        <dd class="text-sm">{{ ucfirst(str_replace('_', ' ', $order->steadfast_delivery_status)) }}</dd>
                    </div>
                    @endif
                </dl>
                <div class="mt-4 flex items-center gap-4">
                    <form action="{{ route('admin.orders.steadfast.refresh', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-primary hover:underline">Refresh status</button>
                    </form>
                    <form action="{{ route('admin.orders.steadfast.remove', $order) }}" method="POST" class="inline" onsubmit="return confirm('Remove Steadfast info from this order? You can create a new parcel later if needed.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:underline">Remove Steadfast info</button>
                    </form>
                </div>
            @else
                <p class="text-sm text-neutral-600 mb-4">Create a parcel with Steadfast Courier for shipping.</p>
                @if(\App\Models\Setting::get('steadfast_api_key') && \App\Models\Setting::get('steadfast_secret_key'))
                    <form action="{{ route('admin.orders.steadfast.parcel', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition font-semibold">
                            Create Parcel (Steadfast)
                        </button>
                    </form>
                @else
                    <p class="text-sm text-amber-600">Configure API Key and Secret Key in <a href="{{ route('admin.settings') }}" class="underline">Settings</a> to create parcels.</p>
                @endif
            @endif
        </div>
        @endif

        <!-- Order Timeline / Logs -->
        @if($order->logs && $order->logs->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Order Timeline</h2>
            <ol class="space-y-4">
                @foreach($order->logs as $log)
                <li class="relative pl-6">
                    <span class="absolute left-0 top-1 w-3 h-3 rounded-full 
                        {{ $log->type === 'status_changed' ? 'bg-blue-500' : 'bg-neutral-400' }}"></span>
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold text-neutral-900">
                            @if($log->type === 'status_changed')
                                Status changed from {{ $log->from_status ? ucfirst($log->from_status) : 'N/A' }} to {{ $log->to_status ? ucfirst($log->to_status) : 'N/A' }}
                            @else
                                {{ ucfirst(str_replace('_', ' ', $log->type)) }}
                            @endif
                        </div>
                        <div class="text-xs text-neutral-500">
                            {{ $log->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-neutral-600">
                        By {{ $log->admin?->name ?? 'System' }}
                    </div>
                    @if($log->notes)
                    <div class="mt-1 text-sm text-neutral-700 whitespace-pre-wrap">
                        {{ $log->notes }}
                    </div>
                    @endif
                </li>
                @endforeach
            </ol>
        </div>
        @endif
    </div>

    <!-- Status Update Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
            <h2 class="text-xl font-bold mb-4">Update Status</h2>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-neutral-700 mb-2">Current Status</label>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' :
                       ($order->status === 'shipped' ? 'bg-purple-100 text-purple-800' :
                       ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                       'bg-red-100 text-red-800'))) }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-neutral-700 mb-2">Change Status</label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-neutral-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Add any notes about this order...">{{ old('notes', $order->notes) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection





