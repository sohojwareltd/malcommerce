<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $bkashService;

    public function __construct(BkashService $bkashService)
    {
        $this->bkashService = $bkashService;
    }

    /**
     * Initiate bKash payment
     */
    public function initiateBkash(Request $request)
    {
        // Support both GET (from redirect) and POST requests
        $orderId = $request->input('order_id') ?: $request->query('order_id');
        
       
        if (!$orderId) {
            return redirect()->route('home')
                ->with('error', 'Order ID is required.');
        }
        $order = Order::find($orderId);
       
        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'Order not found.');
        }

        // Check if order is already paid
        if ($order->payment_status === 'completed') {
            return redirect()->route('orders.success', $order->order_number)
                ->with('error', 'This order is already paid.');
        }

        // Check if payment method is bKash
        if ($order->payment_method !== 'bkash') {
            return redirect()->route('orders.success', $order->order_number)
                ->with('error', 'Invalid payment method.');
        }

        // Generate invoice ID
        $invoiceId = 'INV-' . $order->order_number . '-' . time();

        // Create payment with cancel URL
        $cancelUrl = route('payment.bkash.cancel', ['orderId' => $order->id]);
        
        $result = $this->bkashService->createPayment(
            $order->total_price,
            $invoiceId,
            $order->id,
            $order->customer_phone,
            $cancelUrl
        );
        dd($result);

        // Debug: Check the actual error response
        if (!$result['success']) {
            \Log::error('bKash payment initiation failed', [
                'error' => $result['error'] ?? 'Unknown error',
                'order_id' => $order->id,
                'response' => $result['response'] ?? null,
            ]);
        }

        if ($result['success']) {
            // Update order with payment info
            $order->update([
                'payment_invoice_id' => $invoiceId,
                'payment_transaction_id' => $result['payment_id'],
                'payment_status' => 'processing',
                'payment_response' => json_encode($result['data']),
            ]);

            // Redirect to bKash payment page
            return redirect($result['bkash_url']);
        }

        // Payment initiation failed - delete order and restore stock
        $product = $order->product;
        if ($product) {
            $product->increment('stock_quantity', $order->quantity);
            if ($product->stock_quantity > 0) {
                $product->update(['in_stock' => true]);
            }
        }
        
        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()->route('home')
            ->with('error', 'Payment initiation failed. Your order has been cancelled.');
    }

    /**
     * bKash payment callback
     */
    public function bkashCallback(Request $request)
    {
        $paymentId = $request->input('paymentID');
        $status = $request->input('status');

        if (!$paymentId) {
            Log::error('bKash callback missing paymentID', $request->all());
            return redirect()->route('home')
                ->with('error', 'Invalid payment callback.');
        }

        // Find order by payment transaction ID
        $order = Order::where('payment_transaction_id', $paymentId)
            ->where('payment_status', 'processing')
            ->first();

        if (!$order) {
            Log::error('bKash callback order not found', ['payment_id' => $paymentId]);
            return redirect()->route('home')
                ->with('error', 'Order not found.');
        }

        // If status is not success, delete the order and restore stock
        if ($status !== 'success') {
            // Restore stock before deleting order
            $product = $order->product;
            if ($product) {
                $product->increment('stock_quantity', $order->quantity);
                if ($product->stock_quantity > 0) {
                    $product->update(['in_stock' => true]);
                }
            }
            
            // Delete the order
            $orderNumber = $order->order_number;
            $order->delete();

            return redirect()->route('home')
                ->with('error', 'Payment was cancelled. Your order has been cancelled.');
        }

        // Execute payment to verify
        $result = $this->bkashService->executePayment($paymentId);

        if ($result['success']) {
            $paymentData = $result['data'];

            // Check if payment is successful
            if (isset($paymentData['transactionStatus']) && $paymentData['transactionStatus'] === 'Completed') {
                // Update order
                $order->update([
                    'payment_status' => 'completed',
                    'payment_transaction_id' => $paymentData['trxID'] ?? $paymentId,
                    'payment_response' => json_encode($paymentData),
                    'payment_completed_at' => now(),
                    'status' => 'processing', // Move order to processing after payment
                ]);

                // Send SMS notification
                try {
                    $smsService = app(\App\Services\SmsService::class);
                    $message = "আপনার bKash পেমেন্ট সফল হয়েছে। অর্ডার #{$order->order_number}। ধন্যবাদ!";
                    $smsService->send($order->customer_phone, $message);
                } catch (\Exception $e) {
                    Log::error('Payment success SMS failed', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                return redirect()->route('orders.success', $order->order_number)
                    ->with('success', 'Payment completed successfully!');
            } else {
                // Payment not completed - delete order and restore stock
                $product = $order->product;
                if ($product) {
                    $product->increment('stock_quantity', $order->quantity);
                    if ($product->stock_quantity > 0) {
                        $product->update(['in_stock' => true]);
                    }
                }
                
                $orderNumber = $order->order_number;
                $order->delete();

                return redirect()->route('home')
                    ->with('error', 'Payment verification failed. Your order has been cancelled.');
            }
        } else {
            // Payment execution failed - delete order and restore stock
            $product = $order->product;
            if ($product) {
                $product->increment('stock_quantity', $order->quantity);
                if ($product->stock_quantity > 0) {
                    $product->update(['in_stock' => true]);
                }
            }
            
            $orderNumber = $order->order_number;
            $order->delete();

            return redirect()->route('home')
                ->with('error', 'Payment verification failed. Your order has been cancelled.');
        }
    }

    /**
     * Check payment status
     */
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

        // Query payment status
        $result = $this->bkashService->queryPayment($order->payment_transaction_id);

        if ($result['success']) {
            $paymentData = $result['data'];

            // Update order if payment is completed
            if (isset($paymentData['transactionStatus']) && $paymentData['transactionStatus'] === 'Completed') {
                if ($order->payment_status !== 'completed') {
                    $order->update([
                        'payment_status' => 'completed',
                        'payment_transaction_id' => $paymentData['trxID'] ?? $order->payment_transaction_id,
                        'payment_response' => json_encode($paymentData),
                        'payment_completed_at' => now(),
                        'status' => 'processing',
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
                'data' => $paymentData,
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Failed to query payment status',
        ]);
    }

    /**
     * Cancel payment and delete order
     */
    public function cancelPayment($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('payment_method', 'bkash')
            ->whereIn('payment_status', ['pending', 'processing'])
            ->first();

        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'Order not found or cannot be cancelled.');
        }

        // Restore stock before deleting order
        $product = $order->product;
        if ($product) {
            $product->increment('stock_quantity', $order->quantity);
            if ($product->stock_quantity > 0) {
                $product->update(['in_stock' => true]);
            }
        }
        
        // Delete the order
        $order->delete();

        return redirect()->route('home')
            ->with('error', 'Payment was cancelled. Your order has been cancelled.');
    }
}
