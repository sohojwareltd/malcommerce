<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastService
{
    protected string $baseUrl = 'https://portal.packzy.com/api/v1';

    protected function getHeaders(): array
    {
        $apiKey = Setting::get('steadfast_api_key');
        $secretKey = Setting::get('steadfast_secret_key');

        return [
            'Api-Key' => $apiKey ?? '',
            'Secret-Key' => $secretKey ?? '',
            'Content-Type' => 'application/json',
        ];
    }

    public function isConfigured(): bool
    {
        $apiKey = Setting::get('steadfast_api_key');
        $secretKey = Setting::get('steadfast_secret_key');

        return !empty($apiKey) && !empty($secretKey);
    }

    /**
     * Create a consignment/parcel in Steadfast for an order.
     */
    public function createOrder(Order $order): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Steadfast API is not configured. Please add Api Key and Secret Key in Settings.',
            ];
        }

        // Skip digital products - no physical shipping
        if ($order->product?->is_digital) {
            return [
                'success' => false,
                'error' => 'Steadfast parcel cannot be created for digital products.',
            ];
        }

        // Already has consignment
        if ($order->steadfast_consignment_id) {
            return [
                'success' => false,
                'error' => 'This order already has a Steadfast consignment.',
                'consignment_id' => $order->steadfast_consignment_id,
                'tracking_code' => $order->steadfast_tracking_code,
            ];
        }

        // Build full address
        $addressParts = array_filter([
            $order->address,
            $order->city_village ?? null,
            $order->upazila ?? null,
            $order->district ?? null,
            $order->post_code ?? null,
        ]);
        $recipientAddress = implode(', ', $addressParts);

        // Steadfast requires 11-digit phone (e.g. 01712345678)
        $phone = $this->toLocalPhone($order->customer_phone);

        $payload = [
            'invoice' => 'ORD-' . $order->order_number . '-' . $order->id,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $phone,
            'recipient_address' => substr($recipientAddress, 0, 250),
            'cod_amount' => (float) $order->total_price,
            'note' => $order->notes ? substr($order->notes, 0, 500) : null,
            'item_description' => $order->product?->name ? substr($order->product->name . ' x ' . $order->quantity, 0, 500) : null,
        ];

        if (!empty($order->customer_email)) {
            $payload['recipient_email'] = $order->customer_email;
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/create_order', $payload);

            $body = $response->json();

            if ($response->successful() && isset($body['consignment'])) {
                $consignment = $body['consignment'];
                $order->update([
                    'steadfast_consignment_id' => $consignment['consignment_id'] ?? null,
                    'steadfast_tracking_code' => $consignment['tracking_code'] ?? null,
                    'steadfast_delivery_status' => $consignment['status'] ?? null,
                ]);

                return [
                    'success' => true,
                    'consignment_id' => $consignment['consignment_id'] ?? null,
                    'tracking_code' => $consignment['tracking_code'] ?? null,
                    'status' => $consignment['status'] ?? null,
                ];
            }

            $errorMsg = $body['message'] ?? $body['error'] ?? 'Unknown API error';
            Log::warning('Steadfast create order failed', [
                'order_id' => $order->id,
                'response' => $body,
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'error' => $errorMsg,
            ];
        } catch (\Throwable $e) {
            Log::error('Steadfast create order exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get delivery status by invoice or tracking code.
     */
    public function getStatus(string $invoice): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Steadfast API is not configured.',
            ];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get($this->baseUrl . '/status_by_invoice/' . $invoice);

            $body = $response->json();

            if ($response->successful() && isset($body['delivery_status'])) {
                return [
                    'success' => true,
                    'delivery_status' => $body['delivery_status'],
                ];
            }

            return [
                'success' => false,
                'error' => $body['message'] ?? 'Failed to get status',
            ];
        } catch (\Throwable $e) {
            Log::error('Steadfast get status exception', ['invoice' => $invoice, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get current balance.
     */
    public function getBalance(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Steadfast API is not configured.',
            ];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get($this->baseUrl . '/get_balance');

            $body = $response->json();

            if ($response->successful() && isset($body['current_balance'])) {
                return [
                    'success' => true,
                    'current_balance' => (float) $body['current_balance'],
                ];
            }

            return [
                'success' => false,
                'error' => $body['message'] ?? 'Failed to get balance',
            ];
        } catch (\Throwable $e) {
            Log::error('Steadfast get balance exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Convert phone to 11-digit local format (01XXXXXXXXX) for Steadfast.
     */
    protected function toLocalPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '880') && strlen($digits) >= 13) {
            return '0' . substr($digits, 3);
        }
        if (strlen($digits) === 11 && $digits[0] === '0') {
            return $digits;
        }
        if (strlen($digits) === 10) {
            return '0' . $digits;
        }
        return $phone;
    }
}
