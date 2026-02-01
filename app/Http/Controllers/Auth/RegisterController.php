<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showRegistrationForm()
    {
        // Redirect authenticated users to their dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isSponsor()) {
                return redirect()->route('sponsor.dashboard');
            }
            return redirect()->route('home');
        }
        
        return view('auth.register');
    }
    
    /**
     * Send OTP for registration
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
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

        // Check if user already exists
        if (User::where('phone', $phone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered. Please login instead.'
            ], 422);
        }

        // Check rate limiting
        if (!$this->otpService->canSendOtp($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a moment before requesting another OTP.'
            ], 429);
        }

        // Store name in session for later use
        session(['register_name' => $request->name]);

        // Send OTP
        $result = $this->otpService->sendOtp($phone, 'register');

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
     * Verify OTP and create user
     */
    public function verifyOtp(Request $request)
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
        
        $name = session('register_name');

        if (!$name) {
            return response()->json([
                'success' => false,
                'message' => 'Registration session expired. Please start over.'
            ], 422);
        }

        // Verify OTP
        $result = $this->otpService->verifyOtp($phone, $request->otp, 'register');

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid OTP. Please try again.'
            ], 422);
        }

        // Check if user already exists (double check)
        if (User::where('phone', $phone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered. Please login instead.'
            ], 422);
        }

        // Check if there's a referral code in session
        $sponsorId = null;
        $referralCode = session('referral_code');
        if ($referralCode) {
            $sponsor = User::where('affiliate_code', $referralCode)->first();
            if ($sponsor) {
                $sponsorId = $sponsor->id;
            }
        }

        // Create user as sponsor
        $user = User::create([
            'name' => $name,
            'phone' => $phone,
            'role' => 'sponsor', // All registered users are sponsors
            'sponsor_id' => $sponsorId,
            'password' => null, // No password for OTP-based auth
        ]);

        // Clear session data
        session()->forget(['register_name', 'otp_phone', 'otp_type', 'otp_sent_at']);

        // Login user (always remember - stay logged in until explicit logout)
        Auth::login($user, true);

        // Redirect based on user role (all users are sponsors now)
        $redirectUrl = route('sponsor.dashboard');
        if ($user->isAdmin()) {
            $redirectUrl = route('admin.dashboard');
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'redirect' => $redirectUrl
        ]);
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
