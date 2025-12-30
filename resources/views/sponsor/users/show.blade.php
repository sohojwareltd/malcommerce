@extends('layouts.sponsor')

@section('title', 'Affiliate User Details')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold">Affiliate User Details</h1>
        <p class="text-neutral-600 mt-1">{{ $referral->name }} ({{ $referral->affiliate_code }})</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('sponsor.users.edit', $referral) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition">
            Edit User
        </a>
        <a href="{{ route('sponsor.dashboard') }}" class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
            ← Back to Dashboard
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
                <p class="text-sm font-medium text-neutral-500">Pending Orders</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['pending_orders'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-sm font-medium text-neutral-500">Delivered Orders</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['delivered_orders'] }}</p>
            </div>
        </div>

        <!-- Recent Orders -->
        @if($referral->orders->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Orders ({{ $referral->orders->count() }})</h2>
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
                        @foreach($referral->orders->take(20) as $order)
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900">
                                {{ $order->order_number }}
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
        @else
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Orders</h2>
            <p class="text-neutral-500 text-center py-8">No orders yet</p>
        </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="lg:col-span-1 space-y-6">
        <!-- User Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">User Information</h2>
            
            <!-- Photo -->
            <div class="mb-4 flex justify-center">
                @if($referral->photo)
                    <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-24 h-24 rounded-full object-cover border-2 border-neutral-200">
                @else
                    <div class="w-24 h-24 rounded-full bg-neutral-200 flex items-center justify-center border-2 border-neutral-300">
                        <svg class="w-12 h-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Name</dt>
                    <dd class="mt-1 text-sm font-semibold">{{ $referral->name }}</dd>
                </div>
                
                @if($referral->email)
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Email</dt>
                    <dd class="mt-1 text-sm">{{ $referral->email }}</dd>
                </div>
                @endif
                
                @if($referral->phone)
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Phone</dt>
                    <dd class="mt-1 text-sm">{{ $referral->phone }}</dd>
                </div>
                @endif
            
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Affiliate Code</dt>
                    <dd class="mt-1 text-sm font-mono font-semibold">{{ $referral->affiliate_code }}</dd>
                </div>
                
                @if($referral->address)
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Address</dt>
                    <dd class="mt-1 text-sm whitespace-pre-line">{{ $referral->address }}</dd>
                </div>
                @endif
                
                <div>
                    <dt class="text-sm font-medium text-neutral-500">Joined</dt>
                    <dd class="mt-1 text-sm">{{ $referral->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('sponsor.users.edit', $referral) }}" class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition">
                    Edit User
                </a>
                <a href="{{ route('sponsor.dashboard') }}" class="block w-full text-center px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


