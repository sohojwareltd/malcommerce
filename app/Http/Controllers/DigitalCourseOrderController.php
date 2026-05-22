<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NormalizesBangladeshPhone;
use App\Models\DigitalCourse;
use App\Models\DigitalCourseEnrollment;
use App\Models\DigitalCourseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DigitalCourseOrderController extends Controller
{
    use NormalizesBangladeshPhone;

    public function store(Request $request, DigitalCourse $course)
    {
        if (!$course->is_active) {
            abort(404);
        }

        if ((float) $course->price <= 0) {
            return back()->with('error', 'This course is not available for purchase.');
        }

        if (auth()->check() && DigitalCourseEnrollment::where('user_id', auth()->id())
            ->where('digital_course_id', $course->id)->exists()) {
            return redirect()->route('my-courses.show', $course)
                ->with('info', 'You already have access to this course.');
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

        $price = (float) $course->price;

        $order = DigitalCourseOrder::create([
            'digital_course_id' => $course->id,
            'user_id' => $user->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $normalizedPhone,
            'unit_price' => $price,
            'total_price' => $price,
            'payment_method' => 'bkash',
            'payment_status' => 'pending',
            'status' => DigitalCourseOrder::STATUS_PENDING,
            'sponsor_id' => $sponsorId,
            'referral_code' => $referralCode,
        ]);

        return redirect()->route('payment.course-bkash.initiate', ['order_id' => $order->id]);
    }

    public function success(string $orderNumber)
    {
        $order = DigitalCourseOrder::with('course')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $enrolled = $order->isPaid() && DigitalCourseEnrollment::where('user_id', $order->user_id)
            ->where('digital_course_id', $order->digital_course_id)
            ->exists();

        return view('course-orders.success', compact('order', 'enrolled'));
    }
}
