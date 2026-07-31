<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NormalizesBangladeshPhone;
use App\Models\Exam;
use App\Models\ExamEnrollment;
use App\Models\ExamOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ExamOrderController extends Controller
{
    use NormalizesBangladeshPhone;

    public function store(Request $request, Exam $exam)
    {
        if (!$exam->is_active || !$exam->requiresPayment()) {
            abort(404);
        }

        if ((float) $exam->price <= 0) {
            return back()->with('error', 'This exam is not available for purchase.');
        }

        if (auth()->check() && ExamEnrollment::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)->exists()) {
            return redirect()->route('exams.show', $exam)
                ->with('info', 'You already have access to this exam.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        try {
            $normalizedPhone = $this->normalizePhone($validated['customer_phone']);
        } catch (\Exception $e) {
            return back()->withErrors(['customer_phone' => $e->getMessage()])->withInput();
        }

        $user = User::where('phone', $normalizedPhone)->first();

        if (!$user) {
            $user = User::create([
                'name' => $validated['customer_name'],
                'phone' => $normalizedPhone,
                'role' => 'customer',
                'password' => null,
            ]);
        } elseif ($user->role !== 'customer' && $user->role !== 'admin') {
            $user->update(['name' => $validated['customer_name']]);
        }

        $referralCode = Session::get('referral_code');
        $sponsorId = null;
        if ($referralCode) {
            $sponsor = User::where('affiliate_code', $referralCode)->first();
            if ($sponsor) {
                $sponsorId = $sponsor->id;
            }
        }

        $price = (float) $exam->price;

        $order = ExamOrder::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $normalizedPhone,
            'unit_price' => $price,
            'total_price' => $price,
            'payment_method' => 'bkash',
            'payment_status' => 'pending',
            'status' => ExamOrder::STATUS_PENDING,
            'sponsor_id' => $sponsorId,
            'referral_code' => $referralCode,
        ]);

        return redirect()->route('payment.exam-bkash.initiate', ['order_id' => $order->id]);
    }

    public function success(string $orderNumber)
    {
        $order = ExamOrder::with('exam')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $enrolled = $order->isPaid() && ExamEnrollment::where('user_id', $order->user_id)
            ->where('exam_id', $order->exam_id)
            ->exists();

        return view('exam-orders.success', compact('order', 'enrolled'));
    }
}
