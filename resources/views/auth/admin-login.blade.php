@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Admin Login</h2>
        
        <!-- Login Method Tabs -->
        <div class="flex gap-2 mb-6 border-b border-neutral-200">
            <button id="password-tab" class="flex-1 py-2 px-4 text-center font-semibold border-b-2 border-primary text-primary transition">
                Password
            </button>
            <button id="otp-tab" class="flex-1 py-2 px-4 text-center font-semibold text-neutral-600 hover:text-primary transition">
                OTP
            </button>
        </div>
        
        <!-- Password Login Form -->
        <div id="password-login" class="login-method">
            <form id="password-form" method="POST" action="{{ route('admin.login.password') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" required autofocus 
                           placeholder="admin@example.com" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" required 
                           placeholder="Enter your password"
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
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
        
        <!-- OTP Login Form -->
        <div id="otp-login" class="login-method hidden">
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
    // Tab switching
    const passwordTab = document.getElementById('password-tab');
    const otpTab = document.getElementById('otp-tab');
    const passwordLogin = document.getElementById('password-login');
    const otpLogin = document.getElementById('otp-login');
    
    passwordTab.addEventListener('click', function() {
        passwordTab.classList.add('border-b-2', 'border-primary', 'text-primary');
        passwordTab.classList.remove('text-neutral-600');
        otpTab.classList.remove('border-b-2', 'border-primary', 'text-primary');
        otpTab.classList.add('text-neutral-600');
        passwordLogin.classList.remove('hidden');
        otpLogin.classList.add('hidden');
    });
    
    otpTab.addEventListener('click', function() {
        otpTab.classList.add('border-b-2', 'border-primary', 'text-primary');
        otpTab.classList.remove('text-neutral-600');
        passwordTab.classList.remove('border-b-2', 'border-primary', 'text-primary');
        passwordTab.classList.add('text-neutral-600');
        otpLogin.classList.remove('hidden');
        passwordLogin.classList.add('hidden');
    });
    
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
    phoneInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

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
});
</script>
@endsection

