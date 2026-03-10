@extends('layouts.admin')

@section('title', 'Send SMS')

@section('content')
<div class="mb-4 sm:mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold">Send SMS</h1>
    <p class="text-neutral-600 mt-1 text-sm">Send SMS to manual numbers or to recipients from orders, workshop enrollments, or job applications.</p>
</div>

@if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.sms-send.preview') }}" id="sms-form">
        @csrf

        <div class="border-b border-neutral-200 mb-6">
            <div class="inline-flex rounded-lg border border-neutral-200 overflow-hidden flex-wrap">
                <button type="button" data-tab="manual" class="tab-btn px-4 py-2 text-sm font-semibold {{ old('source', 'manual') === 'manual' ? 'bg-primary/10 text-primary' : 'text-neutral-700 hover:bg-neutral-100' }}">Manual</button>
                <button type="button" data-tab="orders" class="tab-btn px-4 py-2 text-sm font-semibold {{ old('source') === 'orders' ? 'bg-primary/10 text-primary' : 'text-neutral-700 hover:bg-neutral-100' }}">From Orders</button>
                <button type="button" data-tab="enrollments" class="tab-btn px-4 py-2 text-sm font-semibold {{ old('source') === 'enrollments' ? 'bg-primary/10 text-primary' : 'text-neutral-700 hover:bg-neutral-100' }}">From Enrollments</button>
                <button type="button" data-tab="applications" class="tab-btn px-4 py-2 text-sm font-semibold {{ old('source') === 'applications' ? 'bg-primary/10 text-primary' : 'text-neutral-700 hover:bg-neutral-100' }}">From Job Applications</button>
            </div>
        </div>

        <input type="hidden" name="source" id="source" value="{{ old('source', 'manual') }}">

        <div id="panel-manual" class="tab-panel mb-6">
            <label class="block text-sm font-medium text-neutral-700 mb-2">Phone numbers</label>
            <textarea name="manual_numbers" id="manual_numbers" rows="5" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="One number per line or comma-separated">{{ old('manual_numbers') }}</textarea>
            <p class="mt-1 text-xs text-neutral-500">Enter 11-digit Bangladesh mobile numbers (e.g. 01712345678).</p>
        </div>

        <div id="panel-orders" class="tab-panel hidden mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="order_status" class="block text-sm font-medium text-neutral-700 mb-2">Order status</label>
                    <select name="order_status" id="order_status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                        <option value="">All</option>
                        <option value="pending" {{ old('order_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ old('order_status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ old('order_status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ old('order_status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ old('order_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="order_product_id" class="block text-sm font-medium text-neutral-700 mb-2">Product</label>
                    <select name="order_product_id" id="order_product_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                        <option value="">All products</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('order_product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="order_date_from" class="block text-sm font-medium text-neutral-700 mb-2">Date from</label>
                    <input type="date" name="order_date_from" id="order_date_from" value="{{ old('order_date_from') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                </div>
                <div>
                    <label for="order_date_to" class="block text-sm font-medium text-neutral-700 mb-2">Date to</label>
                    <input type="date" name="order_date_to" id="order_date_to" value="{{ old('order_date_to') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                </div>
            </div>
        </div>

        <div id="panel-enrollments" class="tab-panel hidden mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="enrollment_workshop_id" class="block text-sm font-medium text-neutral-700 mb-2">Workshop / Seminar</label>
                    <select name="enrollment_workshop_id" id="enrollment_workshop_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                        <option value="">All</option>
                        @foreach($workshopSeminars as $ws)
                            <option value="{{ $ws->id }}" {{ old('enrollment_workshop_id') == $ws->id ? 'selected' : '' }}>{{ $ws->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="enrollment_status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
                    <select name="enrollment_status" id="enrollment_status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                        <option value="">All</option>
                        <option value="pending" {{ old('enrollment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('enrollment_status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ old('enrollment_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="enrollment_date_from" class="block text-sm font-medium text-neutral-700 mb-2">Enrolled from (date)</label>
                    <input type="date" name="enrollment_date_from" id="enrollment_date_from" value="{{ old('enrollment_date_from') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                </div>
                <div>
                    <label for="enrollment_date_to" class="block text-sm font-medium text-neutral-700 mb-2">Enrolled to (date)</label>
                    <input type="date" name="enrollment_date_to" id="enrollment_date_to" value="{{ old('enrollment_date_to') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                </div>
            </div>
        </div>

        <div id="panel-applications" class="tab-panel hidden mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="application_job_id" class="block text-sm font-medium text-neutral-700 mb-2">Job circular</label>
                    <select name="application_job_id" id="application_job_id" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                        <option value="">All</option>
                        @foreach($jobCirculars as $jc)
                            <option value="{{ $jc->id }}" {{ old('application_job_id') == $jc->id ? 'selected' : '' }}>{{ $jc->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="application_status" class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
                    <select name="application_status" id="application_status" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                        <option value="">All</option>
                        <option value="pending" {{ old('application_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="shortlisted" {{ old('application_status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                        <option value="rejected" {{ old('application_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="hired" {{ old('application_status') === 'hired' ? 'selected' : '' }}>Hired</option>
                    </select>
                </div>
                <div>
                    <label for="application_date_from" class="block text-sm font-medium text-neutral-700 mb-2">Applied from (date)</label>
                    <input type="date" name="application_date_from" id="application_date_from" value="{{ old('application_date_from') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                </div>
                <div>
                    <label for="application_date_to" class="block text-sm font-medium text-neutral-700 mb-2">Applied to (date)</label>
                    <input type="date" name="application_date_to" id="application_date_to" value="{{ old('application_date_to') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-neutral-700 mb-2">Message <span class="text-red-500">*</span></label>
            <textarea name="message" id="message" rows="4" required class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary @error('message') border-red-500 @enderror" placeholder="Type your SMS message here...">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-neutral-500">Placeholder: <code>{name}</code> — replaced with recipient name (or "Customer" if not available).</p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold">Preview recipients</button>
            <a href="{{ route('admin.settings') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sourceInput = document.getElementById('source');
    var panels = document.querySelectorAll('.tab-panel');
    var tabs = document.querySelectorAll('.tab-btn');

    function showPanel(key) {
        sourceInput.value = key;
        panels.forEach(function(p) {
            var id = p.id;
            if (id === 'panel-' + key) {
                p.classList.remove('hidden');
            } else if (id && id.startsWith('panel-')) {
                p.classList.add('hidden');
            }
        });
        tabs.forEach(function(btn) {
            var t = btn.dataset.tab;
            if (t === key) {
                btn.classList.add('bg-primary/10', 'text-primary');
                btn.classList.remove('text-neutral-700');
            } else {
                btn.classList.remove('bg-primary/10', 'text-primary');
                btn.classList.add('text-neutral-700');
            }
        });
    }

    tabs.forEach(function(btn) {
        btn.addEventListener('click', function() {
            showPanel(btn.dataset.tab);
        });
    });

    var initial = sourceInput.value || 'manual';
    showPanel(initial);
});
</script>
@endpush
@endsection
