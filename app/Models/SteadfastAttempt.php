<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SteadfastAttempt extends Model
{
    protected $fillable = [
        'type',
        'notification_type',
        'order_id',
        'success',
        'http_status',
        'ip_address',
        'error_message',
        'request_payload',
        'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'request_payload' => 'array',
            'response_payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function logWebhook(string $notificationType, bool $success, ?int $httpStatus, ?int $orderId, ?string $ip, ?string $errorMessage = null, ?array $requestPayload = null): self
    {
        return self::create([
            'type' => 'webhook',
            'notification_type' => $notificationType ?: 'unknown',
            'order_id' => $orderId,
            'success' => $success,
            'http_status' => $httpStatus,
            'ip_address' => $ip,
            'error_message' => $errorMessage,
            'request_payload' => $requestPayload ? self::sanitizePayload($requestPayload) : null,
        ]);
    }

    public static function logApi(string $type, bool $success, ?int $orderId, ?string $errorMessage = null, ?array $requestPayload = null, ?array $responsePayload = null): self
    {
        return self::create([
            'type' => $type,
            'notification_type' => null,
            'order_id' => $orderId,
            'success' => $success,
            'http_status' => null,
            'ip_address' => null,
            'error_message' => $errorMessage,
            'request_payload' => $requestPayload ? self::sanitizePayload($requestPayload) : null,
            'response_payload' => $responsePayload ? self::sanitizePayload($responsePayload) : null,
        ]);
    }

    protected static function sanitizePayload(array $payload): array
    {
        return $payload;
    }
}
