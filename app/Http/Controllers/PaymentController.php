<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BkashService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(protected BkashService $bkashService)
    {
    }

    public function initiateBkash(Request $request)
    {
        $orderId = $request->input('order_id') ?: $request->query('order_id');
        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Order ID is required.');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        if ($order->payment_status === 'completed') {
            return redirect()->route('orders.success', $order->order_number)
                ->with('error', 'This order is already paid.');
        }

        if ($order->payment_method !== 'bkash') {
            return redirect()->route('orders.success', $order->order_number)
                ->with('error', 'Invalid payment method.');
        }

        $invoiceId = 'INV-' . $order->order_number . '-' . time();
        $result = $this->bkashService->createPayment(
            (float) $order->total_price,
            $invoiceId,
            $order->id,
            $order->customer_phone
        );

        if (!$result['success']) {
            Log::error('bKash payment initiation failed', [
                'order_id' => $order->id,
                'error' => $result['error'] ?? 'Unknown error',
                'response' => $result['data'] ?? null,
            ]);

            $orderNumber = $order->order_number;
            $this->cancelOrderAndRestoreStock($order);

            return $this->paymentCancelled(
                $orderNumber,
                'পেমেন্ট শুরু করা যায়নি। আপনার অর্ডার বাতিল করা হয়েছে।'
            );
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
            Log::error('bKash callback missing paymentID', $request->all());
            return redirect()->route('home')->with('error', 'Invalid payment callback.');
        }

        $order = Order::where('payment_transaction_id', $paymentId)
            ->where('payment_status', 'processing')
            ->first();

        if (!$order) {
            Log::error('bKash callback order not found', ['payment_id' => $paymentId]);
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        if ($status !== 'success') {
            $orderNumber = $order->order_number;
            $this->cancelOrderAndRestoreStock($order);

            return $this->paymentCancelled(
                $orderNumber,
                'আপনি পেমেন্ট বাতিল করেছেন বা পেমেন্ট সম্পন্ন হয়নি। অর্ডার বাতিল করা হয়েছে।'
            );
        }


        try {
        $result = $this->bkashService->executePayment($paymentId);
        } catch (\Throwable $e) {
            $result = $this->bkashService->queryPayment($paymentId);
        }


        if (!$result['success']) {
            $orderNumber = $order->order_number;
            $this->cancelOrderAndRestoreStock($order);

            return $this->paymentCancelled(
                $orderNumber,
                'পেমেন্ট নিশ্চিত করা যায়নি। অর্ডার বাতিল করা হয়েছে।'
            );
        }

        $paymentData = $result['data'] ?? [];
        if (($paymentData['transactionStatus'] ?? null) !== 'Completed') {
            $orderNumber = $order->order_number;
            $this->cancelOrderAndRestoreStock($order);

            return $this->paymentCancelled(
                $orderNumber,
                'পেমেন্ট সম্পন্ন হয়নি। অর্ডার বাতিল করা হয়েছে।'
            );
        }

        $newStatus = 'processing';
        $order->update([
            'payment_status' => 'completed',
            'payment_transaction_id' => $paymentData['trxID'] ?? $paymentId,
            'payment_response' => json_encode($paymentData),
            'payment_completed_at' => now(),
            'status' => $newStatus,
        ]);

        try {
            $smsService = app(SmsService::class);
            $smsService->send($order->customer_phone, "আপনার bKash পেমেন্ট সফল হয়েছে। অর্ডার #{$order->order_number}। ধন্যবাদ!");
        } catch (\Throwable $e) {
            Log::warning('Failed payment-success SMS', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('orders.success', $order->order_number)
            ->with('success', 'Payment completed successfully!');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        if ($order->payment_method !== 'bkash' || !$order->payment_transaction_id) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid payment method or transaction ID',
            ]);
        }

        $result = $this->bkashService->queryPayment($order->payment_transaction_id);
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to query payment status',
            ]);
        }

        $paymentData = $result['data'] ?? [];
        if (($paymentData['transactionStatus'] ?? null) === 'Completed' && $order->payment_status !== 'completed') {
            $order->update([
                'payment_status' => 'completed',
                'payment_transaction_id' => $paymentData['trxID'] ?? $order->payment_transaction_id,
                'payment_response' => json_encode($paymentData),
                'payment_completed_at' => now(),
                'status' => 'processing',
            ]);
        }

        return response()->json([
            'success' => true,
            'payment_status' => $order->fresh()->payment_status,
            'order_status' => $order->fresh()->status,
            'data' => $paymentData,
        ]);
    }

    public function cancelPayment(int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('payment_method', 'bkash')
            ->whereIn('payment_status', ['pending', 'processing'])
            ->first();

        if (!$order) {
            return $this->paymentCancelled(
                null,
                'এই অর্ডারটি পাওয়া যায়নি বা ইতিমধ্যে বাতিল করা হয়েছে।'
            );
        }

        $orderNumber = $order->order_number;
        $this->cancelOrderAndRestoreStock($order);

        return $this->paymentCancelled(
            $orderNumber,
            'পেমেন্ট বাতিল করা হয়েছে। আপনার অর্ডার মুছে ফেলা হয়েছে।'
        );
    }

    protected function paymentCancelled(?string $orderNumber, string $message): \Illuminate\Contracts\View\View
    {
        return view('payment.cancelled', [
            'orderNumber' => $orderNumber,
            'message' => $message,
        ]);
    }

    protected function cancelOrderAndRestoreStock(Order $order): void
    {
        $product = $order->product;
        if ($product && !$product->is_digital) {
            $product->increment('stock_quantity', $order->quantity);
            if ($product->stock_quantity > 0) {
                $product->update(['in_stock' => true]);
            }
        }

        $order->delete();
    }
}
