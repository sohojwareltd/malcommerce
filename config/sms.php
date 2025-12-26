<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS sending service (bulksmsbd.net)
    |
    */

    'api_key' => env('SMS_API_KEY', 'your api key'),
    'sender_id' => env('SMS_SENDER_ID', 'your sender id'),
    'base_url' => env('SMS_BASE_URL', 'http://bulksmsbd.net/api/smsapi'),
    'bulk_url' => env('SMS_BULK_URL', 'http://bulksmsbd.net/api/smsapimany'),
    'brand_name' => env('SMS_BRAND_NAME', null), // Override brand name for SMS (defaults to APP_NAME if not set)

    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    |
    | Additional settings for SMS service
    |
    */

    'enabled' => env('SMS_ENABLED', true),
    'timeout' => env('SMS_TIMEOUT', 30),
];

