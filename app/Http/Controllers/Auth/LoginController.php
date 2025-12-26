<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    /**
     * Send OTP for login
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $phone = $this->normalizePhone($request->phone);

        // Check if user exists
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found. Please register first.'
            ], 422);
        }

        // Check rate limiting
        if (!$this->otpService->canSendOtp($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a moment before requesting another OTP.'
            ], 429);
        }

        // Send OTP
        $result = $this->otpService->sendOtp($phone, 'login');
        

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
     * Verify OTP and login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'otp' => 'required|string|size:6',
        ]);

        $phone = $this->normalizePhone($request->phone);

        // Verify OTP
        $result = $this->otpService->verifyOtp($phone, $request->otp, 'login');

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
                'message' => 'User not found. Please register first.'
            ], 422);
        }

        // Clear session data
        session()->forget(['otp_phone', 'otp_type', 'otp_sent_at']);

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on user role
        $redirectUrl = route('home');
        if ($user->isAdmin()) {
            $redirectUrl = route('admin.dashboard');
        } elseif ($user->isSponsor()) {
            $redirectUrl = route('sponsor.dashboard');
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => $redirectUrl
        ]);
    }

    /**
     * Legacy login method (for backward compatibility if needed)
     */
    public function login(Request $request)
    {
        // For admin/sponsor accounts that might still have passwords
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isSponsor()) {
                return redirect()->intended(route('sponsor.dashboard'));
            }
            
            return redirect()->intended(route('home'));
        }
        
        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }

    /**
     * Normalize phone number
     */
    protected function normalizePhone($phone)
    {
        // Remove spaces, dashes, and other characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it starts with 0, replace with country code
        if (strpos($phone, '0') === 0) {
            $phone = '880' . substr($phone, 1);
        }
        
        // If it doesn't start with country code, add it
        if (strpos($phone, '880') !== 0) {
            $phone = '880' . $phone;
        }
        
        return $phone;
    }
}
