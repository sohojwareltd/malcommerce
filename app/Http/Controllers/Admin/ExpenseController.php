<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);
        $query = Expense::with('category')->latest('expense_date');

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->category);
        }

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }

        $total = (clone $query)->sum('amount');
        $expenses = $query->paginate(20)->withQueryString();
        $categories = ExpenseCategory::orderBy('sort_order')->get();

        return view('admin.expenses.index', compact('expenses', 'categories', 'total'));
    }

    public function create()
    {
        $this->authorize('create', Expense::class);
        $categories = ExpenseCategory::orderBy('sort_order')->get();

        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Expense::class);
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
        ]);

        Expense::create($request->only([
            'expense_category_id',
            'amount',
            'description',
            'expense_date',
        ]));

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        $categories = ExpenseCategory::orderBy('sort_order')->get();

        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->only([
            'expense_category_id',
            'amount',
            'description',
            'expense_date',
        ]));

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Expense::class);
        $query = Expense::with('category')->latest('expense_date');

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->category);
        }
        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }

        $expenses = $query->get();
        $filename = 'expenses-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($expenses) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Category', 'Description', 'Amount']);
            foreach ($expenses as $e) {
                fputcsv($handle, [
                    $e->expense_date->format('Y-m-d'),
                    $e->category?->name ?? '',
                    $e->description ?? '',
                    $e->amount,
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
