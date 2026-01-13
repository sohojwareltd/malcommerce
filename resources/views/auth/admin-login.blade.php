@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Admin Login</h2>
        
        <!-- Phone Check Step (First) -->
        <div id="phone-check-step">
            <form id="check-phone-form">
                @csrf
                
                <div class="mb-4">
                    <label for="check-phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" id="check-phone" required autofocus 
                           placeholder="01XXXXXXXXX" 
                           autocomplete="tel"
                           inputmode="numeric"
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <p class="mt-1 text-xs text-neutral-500">Enter your phone number to continue</p>
                    <div id="check-phone-error" class="mt-1 text-sm text-red-600 hidden"></div>
                </div>
                
                <button type="submit" id="check-phone-btn" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition">
                    Continue
                </button>
            </form>
        </div>
        
        <!-- Password Login Form (Shown if user has password) -->
        <div id="password-login" class="login-method hidden">
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-neutral-700">Login with password or <button type="button" id="switch-to-otp" class="text-primary hover:underline font-semibold">use OTP</button></p>
            </div>
            
            <form id="password-form" method="POST" action="{{ route('admin.login.password') }}">
                @csrf
                <input type="hidden" name="phone" id="password-phone">
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" required 
                           placeholder="admin@example.com" 
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required 
                               placeholder="Enter your password"
                               class="w-full px-4 py-2 pr-12 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-700 focus:outline-none" aria-label="Toggle password visibility">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-primary focus:ring-primary border-neutral-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-neutral-700">Remember me</label>
                </div>
                
                <button type="submit" id="password-submit-btn" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition">
                    Login with Password
                </button>
            </form>
        </div>
        
        <!-- OTP Login Form (Shown if user has no password or chooses OTP) -->
        <div id="otp-login" class="login-method hidden">
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-neutral-700">Login with OTP or <button type="button" id="switch-to-password" class="text-primary hover:underline font-semibold">use password</button></p>
            </div>
            <div id="phone-step">
                <form id="send-otp-form">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" id="phone" required autofocus 
                               placeholder="01XXXXXXXXX" 
                               autocomplete="tel"
                               inputmode="numeric"
                               class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="mt-1 text-xs text-neutral-500">Enter your 11-digit mobile number</p>
                        <div id="phone-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                    
                    <button type="submit" id="send-otp-btn" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition">
                        Send OTP
                    </button>
                </form>
            </div>
            
            <div id="otp-step" class="hidden">
                <div class="mb-4">
                    <p class="text-sm text-neutral-700 mb-4">
                        OTP has been sent to <span id="phone-display"></span>
                    </p>
                </div>
                
                <form id="verify-otp-form">
                    @csrf
                    <input type="hidden" name="phone" id="verify-phone">
                    
                    <div class="mb-4">
                        <label for="otp" class="block text-sm font-medium text-neutral-700 mb-2">OTP Code</label>
                        <input type="text" name="otp" id="otp" required autofocus 
                               placeholder="000000" maxlength="6" 
                               autocomplete="one-time-code"
                               inputmode="numeric"
                               class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-center text-2xl tracking-widest font-mono">
                        <div id="otp-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                    
                    <button type="submit" id="verify-otp-btn" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition mb-3">
                        Verify OTP
                    </button>
                    
                    <button type="button" id="resend-otp-btn" class="w-full text-primary hover:underline text-sm" disabled>
                        <span id="resend-text">Resend OTP (Wait 60 seconds)</span>
                    </button>
                </form>
            </div>
        </div>
        
        <p class="mt-6 text-center text-sm text-neutral-600">
            <a href="{{ route('home') }}" class="text-primary hover:underline">Back to Home</a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const phoneCheckStep = document.getElementById('phone-check-step');
    const passwordLogin = document.getElementById('password-login');
    const otpLogin = document.getElementById('otp-login');
    const checkPhoneForm = document.getElementById('check-phone-form');
    const checkPhoneInput = document.getElementById('check-phone');
    const checkPhoneBtn = document.getElementById('check-phone-btn');
    const checkPhoneError = document.getElementById('check-phone-error');
    const passwordPhoneInput = document.getElementById('password-phone');
    const emailInput = document.getElementById('email');
    const switchToOtpBtn = document.getElementById('switch-to-otp');
    const switchToPasswordBtn = document.getElementById('switch-to-password');
    
    let userPhone = '';
    let userEmail = '';
    let hasPassword = false;
    
    // Format phone input
    checkPhoneInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
    
    // Check phone and determine login method
    checkPhoneForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const phone = checkPhoneInput.value.trim();
        if (!phone || phone.length < 10) {
            showError(checkPhoneError, 'Please enter a valid phone number');
            return;
        }

        checkPhoneBtn.disabled = true;
        checkPhoneBtn.textContent = 'Checking...';
        clearError(checkPhoneError);

        try {
            const response = await fetch('{{ route("admin.login.check-method") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#check-phone-form input[name="_token"]').value
                },
                body: JSON.stringify({ phone: phone })
            });

            const data = await response.json();

            if (data.success) {
                userPhone = phone;
                userEmail = data.email || '';
                hasPassword = data.has_password;
                
                // Hide phone check step
                phoneCheckStep.classList.add('hidden');
                
                if (hasPassword) {
                    // Show password login (default)
                    passwordLogin.classList.remove('hidden');
                    passwordPhoneInput.value = phone;
                    if (userEmail) {
                        emailInput.value = userEmail;
                    }
                    emailInput.focus();
                } else {
                    // Show OTP login (first time user)
                    otpLogin.classList.remove('hidden');
                    phoneInput.value = phone;
                    phoneInput.focus();
                }
            } else {
                showError(checkPhoneError, data.message || 'Phone number not found');
                checkPhoneBtn.disabled = false;
                checkPhoneBtn.textContent = 'Continue';
            }
        } catch (error) {
            showError(checkPhoneError, 'An error occurred. Please try again.');
            checkPhoneBtn.disabled = false;
            checkPhoneBtn.textContent = 'Continue';
        }
    });
    
    // Switch between password and OTP
    if (switchToOtpBtn) {
        switchToOtpBtn.addEventListener('click', function() {
            passwordLogin.classList.add('hidden');
            otpLogin.classList.remove('hidden');
            phoneInput.value = userPhone;
            phoneInput.focus();
        });
    }
    
    if (switchToPasswordBtn) {
        switchToPasswordBtn.addEventListener('click', function() {
            otpLogin.classList.add('hidden');
            passwordLogin.classList.remove('hidden');
            emailInput.focus();
        });
    }
    
    // OTP Login functionality
    const phoneStep = document.getElementById('phone-step');
    const otpStep = document.getElementById('otp-step');
    const sendOtpForm = document.getElementById('send-otp-form');
    const verifyOtpForm = document.getElementById('verify-otp-form');
    const phoneInput = document.getElementById('phone');
    const otpInput = document.getElementById('otp');
    const phoneDisplay = document.getElementById('phone-display');
    const verifyPhoneInput = document.getElementById('verify-phone');
    const phoneError = document.getElementById('phone-error');
    const otpError = document.getElementById('otp-error');
    const sendOtpBtn = document.getElementById('send-otp-btn');
    const verifyOtpBtn = document.getElementById('verify-otp-btn');
    const resendOtpBtn = document.getElementById('resend-otp-btn');
    const resendText = document.getElementById('resend-text');
    
    let resendTimer = null;
    let resendCountdown = 60;
    let otpAbortController = null;

    // Format phone input
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    }

    // OTP input formatting
    otpInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 6);
    });

    // Send OTP
    sendOtpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const phone = phoneInput.value.trim();
        if (!phone || phone.length < 10) {
            showError(phoneError, 'Please enter a valid phone number');
            return;
        }

        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = 'Sending...';
        clearError(phoneError);

        try {
            const response = await fetch('{{ route("admin.login.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#send-otp-form input[name="_token"]').value
                },
                body: JSON.stringify({ phone: phone })
            });

            const data = await response.json();

            if (data.success) {
                phoneStep.classList.add('hidden');
                otpStep.classList.remove('hidden');
                phoneDisplay.textContent = phone;
                verifyPhoneInput.value = phone;
                otpInput.focus();
                startResendTimer();
                startOtpDetection();
            } else {
                showError(phoneError, data.message || 'Failed to send OTP');
                sendOtpBtn.disabled = false;
                sendOtpBtn.textContent = 'Send OTP';
            }
        } catch (error) {
            showError(phoneError, 'An error occurred. Please try again.');
            sendOtpBtn.disabled = false;
            sendOtpBtn.textContent = 'Send OTP';
        }
    });

    // Verify OTP
    verifyOtpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const phone = verifyPhoneInput.value;
        const otp = otpInput.value.trim();

        if (otp.length !== 6) {
            showError(otpError, 'Please enter a valid 6-digit OTP code');
            return;
        }

        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'Verifying...';
        clearError(otpError);

        try {
            const response = await fetch('{{ route("admin.login.verify-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#verify-otp-form input[name="_token"]').value
                },
                body: JSON.stringify({ phone: phone, otp: otp })
            });

            const data = await response.json();

            if (data.success) {
                // If password setup required, show message
                if (data.requires_password_setup) {
                    alert(data.message || 'Please set a password to continue.');
                }
                window.location.href = data.redirect || '{{ route("admin.dashboard") }}';
            } else {
                showError(otpError, data.message || 'Invalid OTP code');
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify OTP';
                otpInput.value = '';
                otpInput.focus();
            }
        } catch (error) {
            showError(otpError, 'An error occurred. Please try again.');
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.textContent = 'Verify OTP';
        }
    });

    // Resend OTP
    resendOtpBtn.addEventListener('click', async function() {
        if (resendOtpBtn.disabled) return;

        const phone = verifyPhoneInput.value;
        resendOtpBtn.disabled = true;

        try {
            const response = await fetch('{{ route("admin.login.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#send-otp-form input[name="_token"]').value
                },
                body: JSON.stringify({ phone: phone })
            });

            const data = await response.json();

            if (data.success) {
                clearError(otpError);
                otpInput.value = '';
                otpInput.focus();
                startResendTimer();
            } else {
                showError(otpError, data.message || 'Failed to send OTP');
                resendOtpBtn.disabled = false;
            }
        } catch (error) {
            showError(otpError, 'An error occurred. Please try again.');
            resendOtpBtn.disabled = false;
        }
    });

    function startResendTimer() {
        if (resendTimer) clearInterval(resendTimer);
        resendCountdown = 60;
        resendOtpBtn.disabled = true;

        resendTimer = setInterval(function() {
            resendCountdown--;
            if (resendCountdown > 0) {
                resendText.textContent = `Resend OTP (Wait ${resendCountdown} seconds)`;
            } else {
                clearInterval(resendTimer);
                resendOtpBtn.disabled = false;
                resendText.textContent = 'Resend OTP';
            }
        }, 1000);
    }

    function showError(element, message) {
        element.textContent = message;
        element.classList.remove('hidden');
    }

    function clearError(element) {
        element.classList.add('hidden');
        element.textContent = '';
    }

    // Web OTP API - Auto-detect OTP from SMS
    function startOtpDetection() {
        if (otpAbortController) {
            otpAbortController.abort();
        }

        if ('OTPCredential' in window) {
            otpAbortController = new AbortController();
            
            navigator.credentials.get({
                otp: { transport: ['sms'] },
                signal: otpAbortController.signal
            }).then(otp => {
                if (otp && otp.code) {
                    otpInput.value = otp.code;
                    if (otp.code.length === 6) {
                        verifyOtpForm.dispatchEvent(new Event('submit'));
                    }
                }
            }).catch(err => {
                console.log('OTP auto-detection not available:', err);
            });
        }
    }

    document.addEventListener('visibilitychange', function() {
        if (document.hidden && otpAbortController) {
            otpAbortController.abort();
        }
    });

    window.addEventListener('beforeunload', function() {
        if (otpAbortController) {
            otpAbortController.abort();
        }
    });

    // Password visibility toggle
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('toggle-password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeOffIcon = document.getElementById('eye-off-icon');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection

