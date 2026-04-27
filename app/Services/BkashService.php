<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class BkashService
{
    protected string $baseUrl;
    protected ?string $username;
    protected ?string $password;
    protected ?string $appKey;
    protected ?string $appSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.bkash.base_url', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
        $this->username = config('services.bkash.username');
        $this->password = config('services.bkash.password');
        $this->appKey = config('services.bkash.app_key');
        $this->appSecret = config('services.bkash.app_secret');
    }

    public function createPayment(
        float $amount,
        string $invoiceId,
        int|string|null $payerReference = null,
        ?string $customerPhone = null
    ): array {
        $callbackUrl = $this->resolveCallbackUrl();

        if (!$callbackUrl) {
            return ['success' => false, 'error' => 'bKash callback URL is not configured.'];
        }

        $payload = [
            'mode' => '0011',
            'payerReference' => (string) ($payerReference ?? '1'),
            'callbackURL' => $callbackUrl,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $invoiceId,
        ];

        if ($customerPhone) {
            $payload['payerReference'] = $customerPhone;
        }

        $response = $this->request('/tokenized/checkout/create', 'POST', $payload, true);
        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];
        if (!isset($data['bkashURL'])) {
            return [
                'success' => false,
                'error' => $data['statusMessage'] ?? 'bKash create payment failed.',
                'data' => $data,
            ];
        }

        return [
            'success' => true,
            'bkash_url' => $data['bkashURL'],
            'payment_id' => $data['paymentID'] ?? null,
            'data' => $data,
        ];
    }

    public function executePayment(string $paymentId): array
    {
        return $this->request('/tokenized/checkout/execute', 'POST', [
            'paymentID' => $paymentId,
        ], true);
    }

    public function queryPayment(string $paymentId): array
    {
        return $this->request('/tokenized/checkout/payment/status', 'POST', [
            'paymentID' => $paymentId,
        ], true);
    }

    public function getAccessToken(): ?string
    {
        $cached = Cache::get('bkash_id_token');
        if ($cached) {
            return $cached;
        }

        $headers = [
            'Content-Type: application/json',
            'username: ' . $this->username,
            'password: ' . $this->password,
        ];

        $payload = [
            'app_key' => $this->appKey,
            'app_secret' => $this->appSecret,
        ];

        $ch = curl_init($this->baseUrl . '/tokenized/checkout/token/grant');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::error('bKash grant token cURL error', ['error' => $error]);
            return null;
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded) || empty($decoded['id_token'])) {
            Log::error('bKash grant token failed', ['response' => $decoded ?: $raw]);
            return null;
        }

        Cache::put('bkash_id_token', $decoded['id_token'], now()->addMinutes(55));

        return $decoded['id_token'];
    }

    protected function request(string $endpoint, string $method, array $payload = [], bool $requiresAuth = false): array
    {
        $headers = ['Content-Type: application/json'];
        if ($requiresAuth) {
            $token = $this->getAccessToken();
            if (!$token) {
                return ['success' => false, 'error' => 'Unable to authenticate with bKash API.'];
            }
            $headers[] = 'Authorization: ' . $token;
            $headers[] = 'X-APP-Key: ' . $this->appKey;
        }

        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        ]);

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::error('bKash API request error', ['endpoint' => $endpoint, 'error' => $error]);
            return ['success' => false, 'error' => $error];
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return ['success' => false, 'error' => 'Invalid bKash API response.', 'raw' => $raw];
        }

        $ok = (($decoded['statusCode'] ?? null) === '0000') || isset($decoded['paymentID']) || isset($decoded['transactionStatus']);

        return [
            'success' => $ok,
            'data' => $decoded,
            'error' => $ok ? null : ($decoded['statusMessage'] ?? 'bKash API request failed.'),
        ];
    }

    protected function resolveCallbackUrl(): ?string
    {
        $configured = trim((string) config('services.bkash.callback_url', ''));
        if ($configured !== '') {
            return $configured;
        }

        if (Route::has('payment.bkash.callback')) {
            try {
                return route('payment.bkash.callback');
            } catch (\Throwable $e) {
                Log::warning('Failed to generate payment callback route URL', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }
}
