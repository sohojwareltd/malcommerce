<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected $smsService;
    protected $otpLength = 6;
    protected $otpExpiry = 5; // minutes

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Generate and send OTP to phone number
     *
     * @param string $phone Phone number
     * @param string $type OTP type: 'register' or 'login'
     * @return array Response array with success status
     */
    public function sendOtp($phone, $type = 'login')
    {
        try {
            // Generate 6-digit OTP
            $otp = $this->generateOtp();
            
            // Store OTP in cache with expiry
            $cacheKey = $this->getCacheKey($phone, $type);
            Cache::put($cacheKey, $otp, now()->addMinutes($this->otpExpiry));
            
            // Store phone number and type in session for verification
            session([
                'otp_phone' => $phone,
                'otp_type' => $type,
                'otp_sent_at' => now()->timestamp
            ]);
            
            // Send OTP via SMS with brand name
            $brandName = config('sms.brand_name') ?: config('app.name', 'Your Brand');
            $message = "Your {$brandName} OTP is {$otp}";
            $smsResult = $this->smsService->sendToSingle($phone, $message);
            
            if ($smsResult['success']) {
                Log::info('OTP sent successfully', [
                    'phone' => $phone,
                    'type' => $type
                ]);
                
                return [
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'otp_expires_in' => $this->otpExpiry * 60 // seconds
                ];
            } else {
                Log::error('Failed to send OTP SMS', [
                    'phone' => $phone,
                    'type' => $type,
                    'error' => $smsResult['error'] ?? 'Unknown error'
                ]);
                
                // Remove OTP from cache if SMS failed
                Cache::forget($cacheKey);
                
                return [
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again.',
                    'error' => $smsResult['error'] ?? 'SMS sending failed'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('OTP generation failed', [
                'phone' => $phone,
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to generate OTP. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify OTP
     *
     * @param string $phone Phone number
     * @param string $otp OTP to verify
     * @param string $type OTP type: 'register' or 'login'
     * @return array Response array with success status
     */
    public function verifyOtp($phone, $otp, $type = 'login')
    {
        try {
            $cacheKey = $this->getCacheKey($phone, $type);
            $storedOtp = Cache::get($cacheKey);
            
            if (!$storedOtp) {
                return [
                    'success' => false,
                    'message' => 'OTP expired or invalid. Please request a new OTP.',
                    'error' => 'OTP not found or expired'
                ];
            }
            
            if ($storedOtp !== $otp) {
                Log::warning('Invalid OTP attempt', [
                    'phone' => $phone,
                    'type' => $type
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.',
                    'error' => 'OTP mismatch'
                ];
            }
            
            // OTP verified successfully, remove it from cache
            Cache::forget($cacheKey);
            
            Log::info('OTP verified successfully', [
                'phone' => $phone,
                'type' => $type
            ]);
            
            return [
                'success' => true,
                'message' => 'OTP verified successfully'
            ];
            
        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'phone' => $phone,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate random OTP
     *
     * @return string 6-digit OTP
     */
    protected function generateOtp()
    {
        return str_pad((string) rand(100000, 999999), $this->otpLength, '0', STR_PAD_LEFT);
    }

    /**
     * Get cache key for OTP
     *
     * @param string $phone
     * @param string $type
     * @return string
     */
    protected function getCacheKey($phone, $type)
    {
        return "otp_{$type}_{$phone}";
    }

    /**
     * Check if OTP was sent recently (rate limiting)
     *
     * @param string $phone
     * @return bool
     */
    public function canSendOtp($phone)
    {
        $lastSent = session('otp_sent_at');
        if (!$lastSent) {
            return true;
        }
        
        // Allow resending OTP after 1 minute
        $cooldown = 60; // seconds
        return (now()->timestamp - $lastSent) >= $cooldown;
    }

    /**
     * Set OTP length
     *
     * @param int $length
     * @return $this
     */
    public function setOtpLength($length)
    {
        $this->otpLength = $length;
        return $this;
    }

    /**
     * Set OTP expiry time in minutes
     *
     * @param int $minutes
     * @return $this
     */
    public function setOtpExpiry($minutes)
    {
        $this->otpExpiry = $minutes;
        return $this;
    }
}

