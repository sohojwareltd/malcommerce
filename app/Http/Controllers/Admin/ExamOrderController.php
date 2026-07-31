<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamOrder;
use App\Models\ExamEnrollment;
use Illuminate\Http\Request;

class ExamOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamOrder::with(['exam', 'user']);

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.exam-orders.index', compact('orders'));
    }

    public function show(ExamOrder $examOrder)
    {
        $examOrder->load(['exam', 'user', 'enrollment']);

        return view('admin.exam-orders.show', compact('examOrder'));
    }

    public function markPaid(ExamOrder $examOrder)
    {
        if ($examOrder->isPaid()) {
            return back()->with('info', 'Order is already paid.');
        }

        $examOrder->update([
            'payment_status' => 'completed',
            'payment_completed_at' => now(),
            'status' => ExamOrder::STATUS_COMPLETED,
        ]);

        ExamEnrollment::grantForOrder($examOrder);

        return back()->with('success', 'Order marked as paid and access granted.');
    }
}
