@extends('layouts.admin')

@section('title', 'Add Expense')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Add Expense</h1>
            <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Record a new expense</p>
        </div>
        <a href="{{ route('admin.expenses.index') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm sm:text-base">
            ← Back
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="expense_category_id" class="block text-sm font-medium text-neutral-700 mb-2">Category <span class="text-red-500">*</span></label>
            <select name="expense_category_id" id="expense_category_id" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('expense_category_id') border-red-500 @enderror">
                <option value="">Select category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('expense_category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-neutral-700 mb-2">Amount <span class="text-red-500">*</span></label>
            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('amount') border-red-500 @enderror"
                placeholder="0.00">
            @error('amount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-neutral-700 mb-2">Description</label>
            <input type="text" name="description" id="description" value="{{ old('description') }}"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('description') border-red-500 @enderror"
                placeholder="e.g. Facebook ad campaign">
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="expense_date" class="block text-sm font-medium text-neutral-700 mb-2">Date <span class="text-red-500">*</span></label>
            <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('expense_date') border-red-500 @enderror">
            @error('expense_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-4 border-t border-neutral-200">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold">
                Add Expense
            </button>
            <a href="{{ route('admin.expenses.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
