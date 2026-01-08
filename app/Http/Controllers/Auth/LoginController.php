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
        
        return view('auth.login');
    }
    
    /**
     * Check login method for user (password or OTP)
     */
    public function checkLoginMethod(Request $request)
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

        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found.',
                'redirect_to_register' => true
            ], 422);
        }
        
        // For admin/sponsor users with password, they should use password login
        // For admin users, redirect to admin login
        if ($user->isAdmin()) {
            return response()->json([
                'success' => true,
                'is_admin' => true,
                'has_password' => !empty($user->password),
                'redirect_to_admin_login' => true,
                'message' => 'Please use admin login page.'
            ]);
        }
        
        // For sponsor users with password, show password login option
        // For sponsor users without password, allow OTP (first time)
        return response()->json([
            'success' => true,
            'is_sponsor' => $user->isSponsor(),
            'has_password' => !empty($user->password),
            'email' => $user->email ?? '',
        ]);
    }
    
    /**
     * Send OTP for login
     */
    public function sendOtp(Request $request)
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
            // Format phone for display (remove country code and add leading 0)
            $displayPhone = $phone;
            if (strpos($displayPhone, '880') === 0) {
                $displayPhone = '0' . substr($displayPhone, 3);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found. Please register first.',
                'redirect_to_register' => true,
                'phone' => $displayPhone
            ], 422);
        }
        
        // If admin/sponsor user has password, they should use password login
        if (($user->isAdmin() || $user->isSponsor()) && !empty($user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Please use password login. OTP login is only available for first-time setup.',
                'use_password_login' => true,
                'phone' => $phone
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

        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

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

        // If user is admin or sponsor and doesn't have password, redirect to profile to set password
        if (($user->isAdmin() || $user->isSponsor()) && empty($user->password)) {
            $redirectUrl = $user->isAdmin() 
                ? route('admin.profile.edit') . '#password'
                : route('sponsor.profile.edit') . '#password';
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful! Please set a password to continue.',
                'redirect' => $redirectUrl,
                'requires_password_setup' => true
            ]);
        }

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
     * Password login method (phone-based)
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'password' => 'required',
        ]);
        
        try {
            $phone = $this->normalizePhone($request->phone);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['phone' => $e->getMessage()]);
        }
        
        // Find user by phone
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'The provided phone number does not match our records.']);
        }
        
        // Check if user has a password
        if (empty($user->password)) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'Password not set. Please use OTP login to set up your password first.']);
        }
        
        // Verify password directly
        if (!\Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['password' => 'The provided password is incorrect.']);
        }
        
        // Login the user directly
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();
        
        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->isSponsor()) {
            return redirect()->intended(route('sponsor.dashboard'));
        }
        
        return redirect()->intended(route('home'));
    }
    
    /**
     * Show admin login form
     * If user has password, show password-first login with OTP option
     * If user doesn't have password, show OTP-only login (first time)
     */
    public function showAdminLoginForm()
    {
        // Redirect authenticated admin users
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.admin-login');
    }
    
    /**
     * Check if admin user has password and return appropriate login method
     */
    public function checkAdminLoginMethod(Request $request)
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

        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found.'
            ], 422);
        }
        
      
        
        // If user has password, they should use password login (with OTP option)
        // If user doesn't have password, they must use OTP (first time)
        return response()->json([
            'success' => true,
            'has_password' => !empty($user->password),
            'email' => $user->email ?? '',
        ]);
    }
    
    /**
     * Admin login with password
     * Also supports phone-based password login
     */
    public function adminLoginPassword(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required',
        ]);
        
        $credentials = [];
        
        // Support both email and phone login
        if ($request->filled('email')) {
            $credentials['email'] = $request->email;
        } elseif ($request->filled('phone')) {
            try {
                $phone = $this->normalizePhone($request->phone);
                $user = User::where('phone', $phone)->first();
                if ($user && $user->isAdmin()) {
                    $credentials['email'] = $user->email;
                } else {
                    return back()->withErrors([
                        'phone' => 'Invalid phone number or you do not have permission.',
                    ])->withInput();
                }
            } catch (\Exception $e) {
                return back()->withErrors([
                    'phone' => $e->getMessage(),
                ])->withInput();
            }
        } else {
            return back()->withErrors([
                'email' => 'Email or phone number is required.',
            ])->withInput();
        }
        
        $credentials['password'] = $request->password;
        
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            
            // Only allow admin users
            if (!$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have permission to access the admin panel.',
                ])->withInput();
            }
            
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return back()->withErrors([
            'password' => 'The provided credentials do not match our records.',
        ])->withInput();
    }
    
    /**
     * Send OTP for admin login
     */
    public function adminSendOtp(Request $request)
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

        // Check if user exists and is admin
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number not found.'
            ], 422);
        }
        
        // Only allow admin users
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access the admin panel.'
            ], 403);
        }

        // Check rate limiting
        if (!$this->otpService->canSendOtp($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a moment before requesting another OTP.'
            ], 429);
        }

        // Send OTP
        $result = $this->otpService->sendOtp($phone, 'admin-login');

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
     * Verify OTP for admin login
     */
    public function adminVerifyOtp(Request $request)
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
        $result = $this->otpService->verifyOtp($phone, $request->otp, 'admin-login');

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid OTP. Please try again.'
            ], 422);
        }

        // Find user and verify admin role
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 422);
        }
        
        // Only allow admin users
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access the admin panel.'
            ], 403);
        }

        // Clear session data
        session()->forget(['otp_phone', 'otp_type', 'otp_sent_at']);

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        // If user doesn't have password, redirect to profile to set password
        if (empty($user->password)) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful! Please set a password to continue.',
                'redirect' => route('admin.profile.edit') . '#password',
                'requires_password_setup' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => route('admin.dashboard')
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
