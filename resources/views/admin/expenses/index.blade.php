@extends('layouts.admin')

@section('title', 'Expenses')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Expenses</h1>
        <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Track and manage business expenses</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.expenses.export', request()->query()) }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base text-center flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export CSV
        </a>
        @can('expenses.create')
        <a href="{{ route('admin.expenses.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base text-center">
            + Add Expense
        </a>
        @endcan
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.expenses.index') }}" class="flex flex-col sm:flex-row gap-4 sm:items-end">
        <div class="sm:w-48">
            <label for="category" class="block text-sm font-medium text-neutral-700 mb-1">Category</label>
            <select name="category" id="category" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:w-48">
            <label for="product_id" class="block text-sm font-medium text-neutral-700 mb-1">Product</label>
            <select name="product_id" id="product_id" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
                <option value="">All</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:w-40">
            <label for="from" class="block text-sm font-medium text-neutral-700 mb-1">From</label>
            <input type="date" name="from" id="from" value="{{ request('from') }}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="sm:w-40">
            <label for="to" class="block text-sm font-medium text-neutral-700 mb-1">To</label>
            <input type="date" name="to" id="to" value="{{ request('to') }}" class="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Filter</button>
            <a href="{{ route('admin.expenses.index') }}" class="bg-neutral-200 text-neutral-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-neutral-300">Clear</a>
        </div>
    </form>
</div>

<!-- Summary -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <div class="text-sm text-neutral-600 mb-1">Total (filtered)</div>
    <div class="text-2xl font-bold text-neutral-900">৳{{ number_format($total, 2) }}</div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($expenses as $expense)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">{{ $expense->expense_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $expense->category?->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $expense->product?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600 max-w-xs truncate">{{ $expense->description ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-neutral-900 text-right">৳{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <div class="flex gap-3 justify-end">
                            @can('expenses.update')
                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-primary hover:text-primary-light font-medium">Edit</a>
                            @endcan
                            @can('expenses.delete')
                            <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-neutral-500">No expenses recorded yet. @can('expenses.create')<a href="{{ route('admin.expenses.create') }}" class="text-primary hover:underline">Add your first expense</a>@endcan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($expenses->hasPages())
<div class="mt-4">
    {{ $expenses->links() }}
</div>
@endif
@endsection
