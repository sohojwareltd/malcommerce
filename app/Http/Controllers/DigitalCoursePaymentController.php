<?php

namespace App\Http\Controllers;

use App\Models\DigitalCourseEnrollment;
use App\Models\DigitalCourseOrder;
use App\Services\BkashService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DigitalCoursePaymentController extends Controller
{
    public function __construct(protected BkashService $bkashService)
    {
    }

    public function initiateBkash(Request $request)
    {
        $orderId = $request->input('order_id') ?: $request->query('order_id');
        if (!$orderId) {
            return redirect()->route('courses.index')->with('error', 'Order ID is required.');
        }

        $order = DigitalCourseOrder::find($orderId);
        if (!$order) {
            return redirect()->route('courses.index')->with('error', 'Order not found.');
        }

        if ($order->payment_status === 'completed') {
            return redirect()->route('course-orders.success', $order->order_number)
                ->with('success', 'This order is already paid.');
        }

        $invoiceId = 'DCINV-' . $order->order_number . '-' . time();
        $callbackUrl = $this->bkashService->resolveCourseCallbackUrl();

        $result = $this->bkashService->createPayment(
            (float) $order->total_price,
            $invoiceId,
            $order->id,
            $order->customer_phone,
            $callbackUrl
        );

        dd($result);
        if (!$result['success']) {
            Log::error('Course bKash initiation failed', [
                'order_id' => $order->id,
                'error' => $result['error'] ?? 'Unknown',
            ]);
            $orderNumber = $order->order_number;
            $order->delete();

            return $this->paymentCancelled($orderNumber, 'পেমেন্ট শুরু করা যায়নি। আপনার অর্ডার বাতিল করা হয়েছে।');
        }

        $order->update([
            'payment_invoice_id' => $invoiceId,
            'payment_transaction_id' => $result['payment_id'],
            'payment_status' => 'processing',
            'payment_response' => json_encode($result['data']),
        ]);

        return redirect($result['bkash_url']);
    }

    public function bkashCallback(Request $request)
    {
        $paymentId = $request->input('paymentID');
        $status = $request->input('status');

        if (!$paymentId) {
            return redirect()->route('courses.index')->with('error', 'Invalid payment callback.');
        }

        $order = DigitalCourseOrder::where('payment_transaction_id', $paymentId)
            ->where('payment_status', 'processing')
            ->first();

        if (!$order) {
            return redirect()->route('courses.index')->with('error', 'Order not found.');
        }

        if ($status !== 'success') {
            $orderNumber = $order->order_number;
            $order->delete();

            return $this->paymentCancelled($orderNumber, 'পেমেন্ট বাতিল হয়েছে। অর্ডার মুছে ফেলা হয়েছে।');
        }

        try {
            $result = $this->bkashService->executePayment($paymentId);
        } catch (\Throwable $e) {
            $result = $this->bkashService->queryPayment($paymentId);
        }

        if (!$result['success']) {
            $orderNumber = $order->order_number;
            $order->delete();

            return $this->paymentCancelled($orderNumber, 'পেমেন্ট নিশ্চিত করা যায়নি।');
        }

        $paymentData = $result['data'] ?? [];
        if (($paymentData['transactionStatus'] ?? null) !== 'Completed') {
            $orderNumber = $order->order_number;
            $order->delete();

            return $this->paymentCancelled($orderNumber, 'পেমেন্ট সম্পন্ন হয়নি।');
        }

        $order->update([
            'payment_status' => 'completed',
            'payment_transaction_id' => $paymentData['trxID'] ?? $paymentId,
            'payment_response' => json_encode($paymentData),
            'payment_completed_at' => now(),
            'status' => DigitalCourseOrder::STATUS_COMPLETED,
        ]);

        DigitalCourseEnrollment::grantForOrder($order);

        try {
            app(SmsService::class)->send(
                $order->customer_phone,
                "আপনার কোর্স কেনা সফল হয়েছে। অর্ডার #{$order->order_number}। লগইন করে ভিডিও দেখুন।"
            );
        } catch (\Throwable $e) {
            Log::warning('Course payment SMS failed', ['order_id' => $order->id]);
        }

        return redirect()->route('course-orders.success', $order->order_number)
            ->with('success', 'Payment completed! Login with your phone to watch the course.');
    }

    public function checkStatus(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:digital_course_orders,id']);

        $order = DigitalCourseOrder::findOrFail($request->order_id);

        if (!$order->payment_transaction_id) {
            return response()->json(['success' => false, 'error' => 'No transaction']);
        }

        $result = $this->bkashService->queryPayment($order->payment_transaction_id);

        if ($result['success']) {
            $paymentData = $result['data'] ?? [];
            if (($paymentData['transactionStatus'] ?? null) === 'Completed' && $order->payment_status !== 'completed') {
                $order->update([
                    'payment_status' => 'completed',
                    'payment_transaction_id' => $paymentData['trxID'] ?? $order->payment_transaction_id,
                    'payment_response' => json_encode($paymentData),
                    'payment_completed_at' => now(),
                    'status' => DigitalCourseOrder::STATUS_COMPLETED,
                ]);
                DigitalCourseEnrollment::grantForOrder($order);
            }
        }

        return response()->json([
            'success' => true,
            'payment_status' => $order->fresh()->payment_status,
            'enrolled' => DigitalCourseEnrollment::where('user_id', $order->user_id)
                ->where('digital_course_id', $order->digital_course_id)->exists(),
        ]);
    }

    public function cancelPayment(int $orderId)
    {
        $order = DigitalCourseOrder::where('id', $orderId)
            ->whereIn('payment_status', ['pending', 'processing'])
            ->first();

        if (!$order) {
            return $this->paymentCancelled(null, 'অর্ডার পাওয়া যায়নি।');
        }

        $orderNumber = $order->order_number;
        $order->delete();

        return $this->paymentCancelled($orderNumber, 'পেমেন্ট বাতিল করা হয়েছে।');
    }

    protected function paymentCancelled(?string $orderNumber, string $message)
    {
        return view('payment.cancelled', [
            'orderNumber' => $orderNumber,
            'message' => $message,
        ]);
    }
}
