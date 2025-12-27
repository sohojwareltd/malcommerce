@extends('layouts.admin')

@section('title', 'Sponsor Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Sponsor Details</h1>
        <p class="text-neutral-600 mt-1">{{ $sponsor->name }} ({{ $sponsor->affiliate_code }})</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition">
            Edit Sponsor
        </a>
        <a href="{{ route('admin.sponsors.index') }}" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
            ← Back to Sponsors
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-sm font-medium text-neutral-500">Total Orders</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-sm font-medium text-neutral-500">Total Revenue</p>
                <p class="text-2xl font-bold text-green-600 mt-1">৳{{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-sm font-medium text-neutral-500">Total Referrals</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['total_referrals'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-sm font-medium text-neutral-500">Delivered Orders</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['delivered_orders'] }}</p>
            </div>
        </div>

        <!-- Product-Specific Partner Links -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Product Partner Links</h2>
            <p class="text-sm text-neutral-600 mb-4">Share these product-specific partner links to track commissions for each product.</p>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($products as $product)
                <div class="border border-neutral-200 rounded-lg p-4 hover:bg-neutral-50 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="font-semibold text-neutral-900 mb-1">{{ $product->name }}</h3>
                            <p class="text-xs text-neutral-500 mb-2">Price: ৳{{ number_format($product->price, 2) }}</p>
                            <div class="flex gap-2 items-center">
                                <input 
                                    type="text" 
                                    value="{{ route('products.show', $product->slug) }}?ref={{ $sponsor->affiliate_code }}" 
                                    readonly 
                                    class="flex-1 px-3 py-2 text-xs border border-neutral-300 rounded bg-neutral-50 font-mono"
                                    id="partner-link-{{ $product->id }}"
                                >
                                <button 
                                    onclick="copyPartnerLink('partner-link-{{ $product->id }}')" 
                                    class="px-3 py-2 bg-primary text-white text-xs rounded hover:bg-primary-light transition whitespace-nowrap"
                                >
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-neutral-500 text-center py-8">No active products found</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        @if($sponsor->orders->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-neutral-200">
                        @foreach($sponsor->orders->take(10) as $order)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $order->product->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">৳{{ number_format($order->total_price, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                       'bg-blue-100 text-blue-800')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Sponsor Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Sponsor Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Name</dt>
                    <dd class="mt-1 text-sm font-semibold">{{ $sponsor->name }}</dd>
                </div>
            
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Partner Code</dt>
                    <dd class="mt-1 text-sm font-mono font-semibold">{{ $sponsor->affiliate_code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Joined</dt>
                    <dd class="mt-1 text-sm">{{ $sponsor->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- General Partner Link -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">General Partner Link</h2>
            <p class="text-sm text-neutral-600 mb-3">Share this link to earn commissions on all products:</p>
            <div class="flex gap-2">
                <input 
                    type="text" 
                    value="{{ url('/') }}?ref={{ $sponsor->affiliate_code }}" 
                    readonly 
                    class="flex-1 px-3 py-2 text-xs border border-neutral-300 rounded bg-neutral-50 font-mono"
                    id="general-partner-link"
                >
                <button 
                    onclick="copyPartnerLink('general-partner-link')" 
                    class="px-3 py-2 bg-primary text-white text-xs rounded hover:bg-primary-light transition whitespace-nowrap"
                >
                    Copy
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition">
                    Edit Sponsor
                </a>
                <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sponsor? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Delete Sponsor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyPartnerLink(inputId) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        navigator.clipboard.writeText(input.value).then(function() {
            // Show temporary success message
            const button = input.nextElementSibling;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.add('bg-green-600');
            button.classList.remove('bg-primary', 'hover:bg-primary-light');
            setTimeout(function() {
                button.textContent = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-primary', 'hover:bg-primary-light');
            }, 2000);
        }).catch(function(err) {
            alert('Failed to copy link');
        });
    }
</script>
@endpush
@endsection

