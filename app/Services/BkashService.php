<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BkashService
{
    protected $baseUrl;
    protected $username;
    protected $password;
    protected $appKey;
    protected $appSecret;
    protected $isSandbox;

    public function __construct()
    {
        // bKash API base URL - provided during onboarding
        // Sandbox: https://tokenized.sandbox.bka.sh/v1.2.0-beta
        // Production: https://tokenized.pay.bka.sh/v1.2.0-beta
        $this->baseUrl = config('services.bkash.base_url', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
        $this->username = config('services.bkash.username');
        $this->password = config('services.bkash.password');
        $this->appKey = config('services.bkash.app_key');
        $this->appSecret = config('services.bkash.app_secret');
        $this->isSandbox = config('services.bkash.sandbox', true);
        
    }

    /**
     * Get access token from bKash
     * Reference: https://developer.bka.sh/docs/grant-token-2
     */
    public function getAccessToken()
    {
        // Check cache first (tokens are valid for 1 hour)
        $cacheKey = 'bkash_access_token';
        $token = Cache::get($cacheKey);
        
        if ($token) {
            return $token;
        }

        try {
            // Grant Token API endpoint
            // Reference: https://developer.bka.sh/docs/grant-token-2
            $endpoint = $this->baseUrl . '/tokenized/checkout/token/grant';
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $this->username,
                'password' => $this->password,
            ])->post($endpoint, [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);
dd($response->body());
            $responseBody = $response->body();
            $responseData = $response->json();
            $statusCode = $response->status();

            if ($response->successful()) {
                if (isset($responseData['id_token'])) {
                    $token = $responseData['id_token'];
                    // Cache token for 50 minutes (tokens usually expire in 1 hour)
                    Cache::put($cacheKey, $token, now()->addMinutes(50));
                    return $token;
                } else {
                    Log::error('bKash token grant: id_token missing in response', [
                        'response' => $responseData,
                        'status' => $statusCode,
                        'endpoint' => $endpoint,
                    ]);
                }
            } else {
                // Extract error message from response
                $errorMessage = $responseData['statusMessage'] ?? 'Unknown error';
                $errorCode = $responseData['statusCode'] ?? $statusCode;
                
                Log::error('bKash token grant failed', [
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'response_body' => $responseBody,
                    'response_data' => $responseData,
                    'status' => $statusCode,
                    'endpoint' => $endpoint,
                    'has_username' => !empty($this->username),
                    'has_password' => !empty($this->password),
                    'has_app_key' => !empty($this->appKey),
                    'has_app_secret' => !empty($this->appSecret),
                ]);
                
                // Return more specific error message
                throw new \Exception("bKash API Error ({$errorCode}): {$errorMessage}");
            }

            return null;
        } catch (\Exception $e) {
            Log::error('bKash token grant exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Create payment
     * Reference: https://developer.bka.sh/reference/createpaymentusingpost
     * 
     * @param float $amount Payment amount
     * @param string $invoiceId Unique merchant invoice number
     * @param int $orderId Order ID for logging
     * @param string|null $customerPhone Customer phone (pre-populates wallet number)
     * @param string|null $cancelUrl Cancel URL (deprecated - bKash generates this from callbackURL)
     * @return array
     */
    public function createPayment($amount, $invoiceId, $orderId, $customerPhone = null, $cancelUrl = null)
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to get access token. Please verify your bKash credentials (APP_KEY, APP_SECRET, USERNAME, PASSWORD) in .env file. Check storage/logs/laravel.log for detailed error message.',
            ];
        }

        // Prepare payment data according to bKash API documentation
        // Reference: https://developer.bka.sh/reference/createpaymentusingpost
        $paymentData = [
            'mode' => '0011', // Checkout (URL) mode - redirects customer to bKash page
            'payerReference' => $customerPhone ?? 'customer', // Pre-populates wallet number if provided
            'callbackURL' => route('payment.bkash.callback'), // Base URL for bKash to generate callback URLs
            'amount' => number_format($amount, 2, '.', ''), // Amount as string with 2 decimal places
            'currency' => 'BDT', // Only BDT is supported
            'intent' => 'sale', // 'sale' for immediate payment, 'authorization' for auth & capture
            'merchantInvoiceNumber' => $invoiceId, // Unique invoice identifier
        ];
        
        // Note: bKash will generate successCallbackURL, failureCallbackURL, and cancelledCallbackURL
        // based on the callbackURL we provide. We don't need to set cancelURL separately.

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/payment/create', $paymentData);

            if ($response->successful()) {
                $data = $response->json();
                
                // According to bKash API documentation, successful response includes:
                // paymentID, bkashURL, successCallbackURL, failureCallbackURL, cancelledCallbackURL
                if (isset($data['paymentID']) && isset($data['bkashURL'])) {
                    return [
                        'success' => true,
                        'payment_id' => $data['paymentID'],
                        'bkash_url' => $data['bkashURL'],
                        'success_callback_url' => $data['successCallbackURL'] ?? null,
                        'failure_callback_url' => $data['failureCallbackURL'] ?? null,
                        'cancelled_callback_url' => $data['cancelledCallbackURL'] ?? null,
                        'data' => $data,
                    ];
                }
            }

            Log::error('bKash payment creation failed', [
                'response' => $response->body(),
                'status' => $response->status(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => $response->json()['statusMessage'] ?? 'Payment creation failed',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('bKash payment creation exception', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Execute payment
     * Reference: https://developer.bka.sh/docs/execute-payment-2
     * 
     * This API must be called after receiving success callback from bKash
     * to complete the payment transaction.
     * 
     * @param string $paymentId Payment ID from Create Payment response
     * @return array
     */
    public function executePayment($paymentId)
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to get access token',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/payment/execute/' . $paymentId);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            Log::error('bKash payment execution failed', [
                'response' => $response->body(),
                'status' => $response->status(),
                'payment_id' => $paymentId,
            ]);

            return [
                'success' => false,
                'error' => $response->json()['statusMessage'] ?? 'Payment execution failed',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('bKash payment execution exception', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Query payment status
     * Reference: https://developer.bka.sh/docs/query-payment-1
     * 
     * Use this API to check payment status if Execute Payment API was not called
     * or if there's uncertainty about payment completion.
     * 
     * @param string $paymentId Payment ID to query
     * @return array
     */
    public function queryPayment($paymentId)
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to get access token',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->get($this->baseUrl . '/tokenized/checkout/payment/query/' . $paymentId);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['statusMessage'] ?? 'Payment query failed',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('bKash payment query exception', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment($paymentId, $amount, $trxId, $sku = 'refund', $reason = 'Customer request')
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to get access token',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/payment/refund', [
                'paymentID' => $paymentId,
                'amount' => number_format($amount, 2, '.', ''),
                'trxID' => $trxId,
                'sku' => $sku,
                'reason' => $reason,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['statusMessage'] ?? 'Refund failed',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('bKash refund exception', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
