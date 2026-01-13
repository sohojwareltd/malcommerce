<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP for password reset
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        // Check if user exists
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found. Please check your phone number and try again.'
            ], 422);
        }

        // Check if user has a password set
        if (empty($user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password not set. Please use OTP login to set up your password first.'
            ], 422);
        }

        // Check rate limiting
        if (!$this->otpService->canSendOtp($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a moment before requesting another OTP.'
            ], 429);
        }

        // Send OTP for password reset
        $result = $this->otpService->sendOtp($phone, 'password-reset');

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your phone number',
                'expires_in' => $result['otp_expires_in'] ?? 300
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send OTP. Please try again.'
        ], 500);
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'otp' => 'required|string|size:6',
        ]);

        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        // Verify OTP
        $result = $this->otpService->verifyOtp($phone, $request->otp, 'password-reset');

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid OTP. Please try again.'
            ], 422);
        }

        // Find user
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 422);
        }

        // Store phone in session for password reset step
        session([
            'password_reset_phone' => $phone,
            'password_reset_verified' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully. You can now reset your password.',
            'redirect' => route('password.reset')
        ]);
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm()
    {
        // Check if password reset is verified
        if (!session('password_reset_verified') || !session('password_reset_phone')) {
            return redirect()->route('password.request')
                ->with('error', 'Please verify your phone number first.');
        }

        return view('auth.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        // Check if password reset is verified
        if (!session('password_reset_verified') || !session('password_reset_phone')) {
            return back()->withErrors([
                'password' => 'Session expired. Please start the password reset process again.'
            ]);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $phone = session('password_reset_phone');
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return back()->withErrors([
                'password' => 'User not found.'
            ]);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session data
        session()->forget(['password_reset_phone', 'password_reset_verified']);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully! Please login with your new password.');
    }

    /**
     * Normalize and validate Bangladesh phone number
     * Formats: 01795560431 -> 8801795560431, 8801795560431 -> 8801795560431, +8801795560431 -> 8801795560431
     *
     * @param string $phone Phone number in any format
     * @return string Normalized phone number (880XXXXXXXXXX)
     * @throws \Exception If phone number is invalid
     */
    protected function normalizePhone($phone)
    {
        if (empty($phone)) {
            throw new \Exception('Phone number is required');
        }

        // Remove all non-numeric characters except + (we'll handle + separately)
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove + if present
        $phone = str_replace('+', '', $phone);
        
        // If it starts with 880, validate format
        if (strpos($phone, '880') === 0) {
            // Validate Bangladesh mobile format: 880 + 1 + 9 digits = 13 digits total
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
        }
        
        // If it starts with 0, replace with 880
        if (strpos($phone, '0') === 0) {
            $phone = '880' . substr($phone, 1);
            // Validate: should be 13 digits total and 4th digit should be 1
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
        }
        
        // If it doesn't start with 0 or 880, add 880 prefix
        $phone = '880' . $phone;
        // Validate: should be 13 digits total and 4th digit should be 1
        if (strlen($phone) === 13 && $phone[3] === '1') {
            return $phone;
        }
        
        throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
    }
}
