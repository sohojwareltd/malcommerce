@extends('layouts.sponsor')

@section('title', 'Partner Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Partner Dashboard</h1>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-neutral-600 text-sm font-medium mb-2">Total Referrals</h3>
        <p class="text-3xl font-bold text-primary">{{ $stats['total_referrals'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-neutral-600 text-sm font-medium mb-2">Total Orders</h3>
        <p class="text-3xl font-bold text-primary">{{ $stats['total_orders'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-neutral-600 text-sm font-medium mb-2">Total Revenue</h3>
        <p class="text-3xl font-bold text-accent">৳{{ number_format($stats['total_revenue'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-neutral-600 text-sm font-medium mb-2">Pending Orders</h3>
        <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_orders'] }}</p>
    </div>
</div>

<!-- General Partner Link -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-xl font-bold mb-4">General Partner Link</h2>
    <div class="flex gap-2">
        <input type="text" value="{{ $affiliateLink }}" readonly class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg bg-neutral-50" id="general-partner-link">
        <button onclick="copyPartnerLink('general-partner-link')" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
            Copy Link
        </button>
    </div>
    <p class="text-sm text-neutral-500 mt-2">Share this link to earn commissions on all products!</p>
</div>

<!-- Product-Specific Partner Links -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
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
                            value="{{ route('products.show', $product->slug) }}?ref={{ Auth::user()->affiliate_code }}" 
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
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($recentOrders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">৳{{ number_format($order->total_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-neutral-500">No orders yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Referrals -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4">Your Referrals</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Joined</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($referrals as $referral)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">{{ $referral->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $referral->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $referral->orders_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ $referral->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-neutral-500">No referrals yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
        // Fallback for older browsers
        document.execCommand('copy');
        alert('Partner link copied to clipboard!');
    });
}
</script>
@endpush
@endsection


