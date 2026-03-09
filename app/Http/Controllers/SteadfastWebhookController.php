<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Setting;
use App\Models\SteadfastAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SteadfastWebhookController extends Controller
{
    /**
     * Handle Steadfast webhook POST requests.
     * Supports: delivery_status, tracking_update
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Optional: Verify Bearer token if API key is configured
        $apiKey = Setting::get('steadfast_api_key');
        if ($apiKey) {
            $authHeader = $request->header('Authorization');
            if (!$authHeader || $authHeader !== 'Bearer ' . $apiKey) {
                SteadfastAttempt::logWebhook('webhook_auth_fail', false, 401, null, $request->ip(), 'Unauthorized', $request->all());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized.',
                ], 401);
            }
        }

        $payload = $request->all();
        $notificationType = $payload['notification_type'] ?? null;

        if (!$notificationType) {
            SteadfastAttempt::logWebhook('unknown', false, 400, null, $request->ip(), 'Missing notification_type', $payload);
            Log::warning('Steadfast webhook: missing notification_type', ['payload' => $payload]);
            return response()->json([
                'status' => 'error',
                'message' => 'Missing notification_type.',
            ], 400);
        }

        if ($notificationType === 'delivery_status') {
            return $this->handleDeliveryStatus($payload, $request->ip());
        }

        if ($notificationType === 'tracking_update') {
            return $this->handleTrackingUpdate($payload, $request->ip());
        }

        SteadfastAttempt::logWebhook($notificationType, true, 200, null, $request->ip(), null, $payload);
        Log::info('Steadfast webhook: unknown notification_type', ['type' => $notificationType]);
        return response()->json([
            'status' => 'success',
            'message' => 'Webhook received (ignored).',
        ]);
    }

    protected function handleDeliveryStatus(array $payload, ?string $ip = null): JsonResponse
    {
        $order = $this->findOrder($payload);

        if (!$order) {
            SteadfastAttempt::logWebhook('delivery_status', false, 404, null, $ip, 'Order not found', $payload);
            Log::warning('Steadfast webhook delivery_status: order not found', ['payload' => $payload]);
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid consignment ID or invoice.',
            ], 404);
        }

        $status = $this->normalizeStatus($payload['status'] ?? null);
        $previousStatus = $order->steadfast_delivery_status;

        $order->update([
            'steadfast_delivery_status' => $status,
        ]);

        OrderLog::create([
            'order_id' => $order->id,
            'admin_id' => null,
            'type' => 'steadfast_delivery_status',
            'from_status' => $previousStatus,
            'to_status' => $status,
            'notes' => $payload['tracking_message'] ?? null,
            'meta' => [
                'consignment_id' => $payload['consignment_id'] ?? null,
                'invoice' => $payload['invoice'] ?? null,
                'cod_amount' => $payload['cod_amount'] ?? null,
                'delivery_charge' => $payload['delivery_charge'] ?? null,
                'updated_at' => $payload['updated_at'] ?? null,
            ],
        ]);

        SteadfastAttempt::logWebhook('delivery_status', true, 200, $order->id, $ip, null, $payload);
        Log::info('Steadfast webhook: delivery status updated', [
            'order_id' => $order->id,
            'status' => $status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook received successfully.',
        ]);
    }

    protected function handleTrackingUpdate(array $payload, ?string $ip = null): JsonResponse
    {
        $order = $this->findOrder($payload);

        if (!$order) {
            SteadfastAttempt::logWebhook('tracking_update', false, 404, null, $ip, 'Order not found', $payload);
            Log::warning('Steadfast webhook tracking_update: order not found', ['payload' => $payload]);
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid consignment ID or invoice.',
            ], 404);
        }

        $trackingMessage = $payload['tracking_message'] ?? null;

        OrderLog::create([
            'order_id' => $order->id,
            'admin_id' => null,
            'type' => 'steadfast_tracking_update',
            'from_status' => null,
            'to_status' => null,
            'notes' => $trackingMessage,
            'meta' => [
                'consignment_id' => $payload['consignment_id'] ?? null,
                'invoice' => $payload['invoice'] ?? null,
                'updated_at' => $payload['updated_at'] ?? null,
            ],
        ]);

        SteadfastAttempt::logWebhook('tracking_update', true, 200, $order->id, $ip, null, $payload);
        Log::info('Steadfast webhook: tracking update logged', [
            'order_id' => $order->id,
            'message' => $trackingMessage,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook received successfully.',
        ]);
    }

    protected function findOrder(array $payload): ?Order
    {
        $consignmentId = $payload['consignment_id'] ?? null;
        $invoice = $payload['invoice'] ?? null;

        // Match by consignment_id
        if ($consignmentId) {
            $order = Order::where('steadfast_consignment_id', $consignmentId)->first();
            if ($order) {
                return $order;
            }
        }

        // Match by invoice: ORD-{order_number}-{order_id}
        if ($invoice && preg_match('/^ORD-(\d+)-(\d+)$/i', trim($invoice), $m)) {
            $orderId = (int) $m[2];
            $orderNumber = $m[1];
            $order = Order::find($orderId);
            if ($order && $order->order_number === $orderNumber) {
                return $order;
            }
        }

        return null;
    }

    protected function normalizeStatus(?string $status): ?string
    {
        if (empty($status)) {
            return null;
        }
        return strtolower(trim($status));
    }
}
