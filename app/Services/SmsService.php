<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $senderId;
    protected $baseUrl;
    protected $bulkUrl;

    public function __construct()
    {
        $this->apiKey = config('sms.api_key');
        $this->senderId = config('sms.sender_id');
        $this->baseUrl = config('sms.base_url', 'http://bulksmsbd.net/api/smsapi');
        $this->bulkUrl = config('sms.bulk_url', 'http://bulksmsbd.net/api/smsapimany');
    }

    /**
     * Send SMS to single or multiple numbers
     *
     * @param string|array $numbers Phone number(s) - can be single number or comma-separated string or array
     * @param string $message SMS message content
     * @return array Response array with success status and message
     */
    public function send($numbers, $message)
    {
        try {
            // Validate API credentials
            if (empty($this->apiKey) || empty($this->senderId)) {
                return [
                    'success' => false,
                    'message' => 'SMS API credentials not configured',
                    'error' => 'Missing API key or sender ID'
                ];
            }

            // Normalize phone numbers
            $numberString = $this->normalizeNumbers($numbers);

            if (empty($numberString)) {
                return [
                    'success' => false,
                    'message' => 'No valid Bangladesh phone numbers provided. Please enter a valid 11-digit mobile number.',
                    'error' => 'Invalid phone numbers'
                ];
            }

            // Validate message
            if (empty($message)) {
                return [
                    'success' => false,
                    'message' => 'SMS message cannot be empty',
                    'error' => 'Empty message'
                ];
            }

            // Prepare data
            $data = [
                'api_key' => $this->apiKey,
                'senderid' => $this->senderId,
                'number' => $numberString,
                'message' => $message
            ];

            // Send SMS
            $response = $this->makeRequest($data);

            // Parse response
            return $this->parseResponse($response);

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'error' => $e->getMessage(),
                'numbers' => is_array($numbers) ? implode(',', $numbers) : $numbers,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS to a single number
     *
     * @param string $number Phone number
     * @param string $message SMS message content
     * @return array Response array
     */
    public function sendToSingle($number, $message)
    {
        return $this->send($number, $message);
    }

    /**
     * Send SMS to multiple numbers with the same message
     *
     * @param array $numbers Array of phone numbers
     * @param string $message SMS message content
     * @return array Response array
     */
    public function sendToMultiple(array $numbers, $message)
    {
        return $this->send($numbers, $message);
    }

    /**
     * Send multiple SMS with different messages to different recipients
     *
     * @param array $messages Array of message objects with 'to' and 'message' keys
     *                        Example: [
     *                            ['to' => '8801612345678', 'message' => 'Message 1'],
     *                            ['to' => '8801912345678', 'message' => 'Message 2']
     *                        ]
     * @return array Response array with success status and message
     */
    public function sendMany(array $messages)
    {
        try {
            // Validate API credentials
            if (empty($this->apiKey) || empty($this->senderId)) {
                return [
                    'success' => false,
                    'message' => 'SMS API credentials not configured',
                    'error' => 'Missing API key or sender ID'
                ];
            }

            // Validate messages array
            if (empty($messages) || !is_array($messages)) {
                return [
                    'success' => false,
                    'message' => 'Messages array is required',
                    'error' => 'Invalid messages array'
                ];
            }

            // Validate each message
            $validMessages = [];
            foreach ($messages as $index => $message) {
                if (!isset($message['to']) || empty($message['to'])) {
                    continue; // Skip invalid messages
                }
                
                if (!isset($message['message']) || empty($message['message'])) {
                    continue; // Skip messages without content
                }

                // Normalize and validate phone number
                $normalizedPhone = $this->normalizePhone(trim($message['to']));
                if (!$normalizedPhone) {
                    continue; // Skip invalid phone numbers
                }

                $validMessages[] = [
                    'to' => $normalizedPhone,
                    'message' => $message['message']
                ];
            }

            if (empty($validMessages)) {
                return [
                    'success' => false,
                    'message' => 'No valid messages to send',
                    'error' => 'All messages are invalid'
                ];
            }

            // Prepare data
            $data = [
                'api_key' => $this->apiKey,
                'senderid' => $this->senderId,
                'messages' => json_encode($validMessages)
            ];

            // Send SMS
            $response = $this->makeBulkRequest($data);

            // Parse response
            return $this->parseResponse($response);

        } catch (\Exception $e) {
            Log::error('Bulk SMS sending failed', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send bulk SMS',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Normalize phone numbers to the required format
     *
     * @param string|array $numbers
     * @return string Comma-separated phone numbers
     */
    protected function normalizeNumbers($numbers)
    {
        if (is_array($numbers)) {
            // Normalize each number
            $normalized = [];
            foreach ($numbers as $number) {
                $normalizedNumber = $this->normalizePhone($number);
                if ($normalizedNumber) {
                    $normalized[] = $normalizedNumber;
                }
            }
            return implode(',', $normalized);
        }

        // If it's a string, split by comma, normalize each, and rejoin
        $numberArray = explode(',', $numbers);
        $normalized = [];
        foreach ($numberArray as $number) {
            $normalizedNumber = $this->normalizePhone(trim($number));
            if ($normalizedNumber) {
                $normalized[] = $normalizedNumber;
            }
        }
        return implode(',', $normalized);
    }

    /**
     * Normalize and validate Bangladesh phone number
     * Formats: 01795560431 -> 8801795560431, 8801795560431 -> 8801795560431, +8801795560431 -> 8801795560431
     *
     * @param string $phone Phone number in any format
     * @return string|null Normalized phone number (880XXXXXXXXXX) or null if invalid
     */
    protected function normalizePhone($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-numeric characters except + (we'll handle + separately)
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove + if present
        $phone = str_replace('+', '', $phone);
        
        // If it starts with 880, keep it as is (already normalized)
        if (strpos($phone, '880') === 0) {
            // Validate Bangladesh mobile format: 880 + 1 + 9 digits = 13 digits total
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            return null; // Invalid format
        }
        
        // If it starts with 0, replace with 880
        if (strpos($phone, '0') === 0) {
            $phone = '880' . substr($phone, 1);
            // Validate: should be 13 digits total and 4th digit should be 1
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            return null; // Invalid format
        }
        
        // If it doesn't start with 0 or 880, add 880 prefix
        $phone = '880' . $phone;
        // Validate: should be 13 digits total and 4th digit should be 1
        if (strlen($phone) === 13 && $phone[3] === '1') {
            return $phone;
        }
        
        return null; // Invalid format
    }

    /**
     * Validate Bangladesh phone number
     *
     * @param string $phone Phone number
     * @return bool True if valid Bangladesh mobile number
     */
    protected function isValidBangladeshPhone($phone)
    {
        $normalized = $this->normalizePhone($phone);
        return $normalized !== null;
    }

    /**
     * Make HTTP request to SMS API (single message)
     *
     * @param array $data
     * @return string|false Response body or false on failure
     */
    protected function makeRequest(array $data)
    {
        return $this->makeHttpRequest($this->baseUrl, $data);
    }

    /**
     * Make HTTP request to Bulk SMS API (multiple messages)
     *
     * @param array $data
     * @return string|false Response body or false on failure
     */
    protected function makeBulkRequest(array $data)
    {
        return $this->makeHttpRequest($this->bulkUrl, $data);
    }

    /**
     * Make HTTP request helper
     *
     * @param string $url
     * @param array $data
     * @return string|false Response body or false on failure
     */
    protected function makeHttpRequest($url, array $data)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($error) {
            throw new \Exception("cURL Error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new \Exception("HTTP Error: {$httpCode}");
        }

        // Log raw response for debugging (only in debug mode)
        if (config('app.debug')) {
            Log::debug('SMS API Raw Response', [
                'raw_response' => $response,
                'response_length' => strlen($response),
                'http_code' => $httpCode,
                'is_numeric' => is_numeric(trim($response))
            ]);
        }

        return $response;
    }

    /**
     * Parse API response
     *
     * @param string $response Raw API response
     * @return array Parsed response
     */
    protected function parseResponse($response)
    {
        // Trim whitespace, newlines, and any invisible characters
        $response = trim($response, " \t\n\r\0\x0B");
        
        // Try to decode JSON response
        $decoded = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // If it's a JSON response, check for success indicators
            if (isset($decoded['success']) || isset($decoded['status'])) {
                return [
                    'success' => $decoded['success'] ?? ($decoded['status'] === 'success'),
                    'message' => $decoded['message'] ?? $response,
                    'data' => $decoded
                ];
            }
        }

        // Extract numeric code from response (handles "202", " 202 ", "202\n", etc.)
        $code = null;
        if (is_numeric($response)) {
            // Direct numeric response like "202"
            $code = (int) $response;
        } elseif (preg_match('/^\s*(\d{3,4})\s*$/', $response, $matches)) {
            // Numeric code with whitespace
            $code = (int) $matches[1];
        } elseif (preg_match('/\b(\d{3,4})\b/', $response, $matches)) {
            // Numeric code within text
            $code = (int) $matches[1];
        }

        // If we found a numeric code, use the numeric response parser
        if ($code !== null) {
            return $this->parseNumericResponse($code);
        }

        // Fallback: Check for common success indicators in plain text
        $lowerResponse = strtolower($response);
        $success = (
            strpos($lowerResponse, 'success') !== false ||
            strpos($lowerResponse, 'sent') !== false ||
            strpos($lowerResponse, 'ok') !== false
        ) && strpos($lowerResponse, 'error') === false;

        return [
            'success' => $success,
            'message' => $this->getResponseMessage($response),
            'raw' => $response,
            'code' => null
        ];
    }

    /**
     * Parse numeric response code from bulksmsbd.net API
     *
     * @param int $code Response code
     * @return array Parsed response
     */
    protected function parseNumericResponse($code)
    {
        $message = $this->getResponseMessageByCode($code);
        $success = $code === 202;

        return [
            'success' => $success,
            'message' => $message,
            'code' => $code,
            'error' => $success ? null : $message
        ];
    }

    /**
     * Get response message by code
     *
     * @param int $code Response code
     * @return string Message
     */
    protected function getResponseMessageByCode($code)
    {
        $messages = [
            202 => 'SMS submitted successfully',
            1001 => 'Invalid phone number. Please check the number and try again.',
            1002 => 'Sender ID is incorrect or disabled. Please contact administrator.',
            1003 => 'Required fields are missing. Please contact system administrator.',
            1005 => 'Internal server error. Please try again later.',
            1006 => 'Balance validity not available. Please contact administrator.',
            1007 => 'Insufficient balance. Please contact administrator to recharge.',
            1011 => 'User ID not found. Please contact administrator.',
            1012 => 'Masking SMS must be sent in Bengali.',
            1013 => 'Sender ID gateway not found for this API key.',
            1014 => 'Sender type name not found for this sender ID.',
            1015 => 'No valid gateway found for this sender ID.',
            1016 => 'Active price information not found for this sender ID.',
            1017 => 'Price information not found for this sender ID.',
            1018 => 'Account is disabled. Please contact administrator.',
            1019 => 'Sender type price is disabled for this account.',
            1020 => 'Parent account not found.',
            1021 => 'Parent account active price not found.',
            1031 => 'Account not verified. Please contact administrator.',
            1032 => 'IP address not whitelisted. Please contact administrator.',
        ];

        return $messages[$code] ?? "Unknown error (Code: {$code})";
    }

    /**
     * Get response message from raw response
     *
     * @param string $response Raw response
     * @return string Message
     */
    protected function getResponseMessage($response)
    {
        // If it's a numeric code, use the code mapping
        if (is_numeric($response)) {
            return $this->getResponseMessageByCode((int) $response);
        }

        // Check for numeric codes in the response
        if (preg_match('/\b(\d{4})\b/', $response, $matches)) {
            $code = (int) $matches[1];
            $message = $this->getResponseMessageByCode($code);
            if ($message !== "Unknown error (Code: {$code})") {
                return $message;
            }
        }

        // Default messages for common patterns
        $lowerResponse = strtolower($response);
        if (strpos($lowerResponse, 'success') !== false) {
            return 'SMS sent successfully';
        }
        if (strpos($lowerResponse, 'error') !== false) {
            return 'Error sending SMS. Please try again.';
        }

        return $response;
    }

    /**
     * Set API key dynamically
     *
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Set sender ID dynamically
     *
     * @param string $senderId
     * @return $this
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * Set base URL dynamically
     *
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * Set bulk URL dynamically
     *
     * @param string $bulkUrl
     * @return $this
     */
    public function setBulkUrl($bulkUrl)
    {
        $this->bulkUrl = $bulkUrl;
        return $this;
    }
}

