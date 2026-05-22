<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalCourseEnrollment;
use App\Models\DigitalCourseOrder;
use Illuminate\Http\Request;

class DigitalCourseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DigitalCourseOrder::query()
            ->with(['course', 'user', 'enrollment']);

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('admin.digital-courses.orders.index', compact('orders'));
    }

    public function show(DigitalCourseOrder $digitalCourseOrder)
    {
        $digitalCourseOrder->load(['course', 'user', 'enrollment', 'sponsor']);

        return view('admin.digital-courses.orders.show', ['order' => $digitalCourseOrder]);
    }

    public function markPaid(DigitalCourseOrder $digitalCourseOrder)
    {
        if ($digitalCourseOrder->payment_status === 'completed') {
            return back()->with('error', 'Order is already paid.');
        }

        $digitalCourseOrder->update([
            'payment_status' => 'completed',
            'status' => DigitalCourseOrder::STATUS_COMPLETED,
            'payment_completed_at' => now(),
        ]);

        DigitalCourseEnrollment::grantForOrder($digitalCourseOrder);

        return back()->with('success', 'Order marked as paid and enrollment granted.');
    }
}
