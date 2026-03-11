@extends('layouts.admin')

@section('title', 'Expense Categories')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Expense Categories</h1>
        <p class="text-neutral-600 mt-1 text-sm sm:text-base">Organize your expenses into categories</p>
    </div>
    <a href="{{ route('admin.expense-categories.create') }}" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base text-center">
        + Add Category
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Sort Order</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($categories as $category)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-600 max-w-md truncate">{{ $category->description }}</td>
                    <td class="px-6 py-4 text-sm text-neutral-500 text-right">{{ $category->sort_order ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-right space-x-2">
                        <a href="{{ route('admin.expense-categories.edit', $category) }}" class="text-primary hover:text-primary-light font-semibold">Edit</a>
                        <form action="{{ route('admin.expense-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-neutral-500">No expense categories yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-neutral-200">
        {{ $categories->links() }}
    </div>
</div>
@endsection

