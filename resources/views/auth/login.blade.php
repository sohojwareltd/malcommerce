@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6 font-bangla">লগইন করুন</h2>
        
        <!-- Login Method Tabs -->
        <div class="flex gap-2 mb-6 border-b border-neutral-200">
            <button id="password-tab" class="flex-1 py-2 px-4 text-center font-semibold {{ $errors->has('phone') || $errors->has('password') ? 'border-b-2 border-primary text-primary' : 'text-neutral-600 hover:text-primary' }} transition">
                Password
            </button>
            <button id="otp-tab" class="flex-1 py-2 px-4 text-center font-semibold {{ $errors->has('phone') || $errors->has('password') ? 'text-neutral-600 hover:text-primary' : 'border-b-2 border-primary text-primary' }} transition">
                OTP
            </button>
        </div>
        
        <!-- Password Login Form -->
        <div id="password-login" class="login-method {{ $errors->has('phone') || $errors->has('password') ? '' : 'hidden' }}">
            <form id="password-form" method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="password-phone" class="block text-sm font-medium text-neutral-700 mb-2 font-bangla">ফোন নম্বর</label>
                    <input type="tel" name="phone" id="password-phone" required autofocus 
                           value="{{ old('phone') }}"
                           placeholder="01XXXXXXXXX" 
                           autocomplete="tel"
                           inputmode="numeric"
                           class="w-full px-4 py-2 border {{ $errors->has('phone') ? 'border-red-500' : 'border-neutral-300' }} rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla">
                    <p class="mt-1 text-xs text-neutral-500 font-bangla">আপনার ১১ সংখ্যার মোবাইল নম্বর দিন</p>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-neutral-700 mb-2 font-bangla">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required 
                               placeholder="Enter your password"
                               class="w-full px-4 py-2 pr-12 border {{ $errors->has('password') ? 'border-red-500' : 'border-neutral-300' }} rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
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
                    <label for="remember" class="ml-2 block text-sm text-neutral-700 font-bangla">Remember me</label>
                </div>
                
                <button type="submit" id="password-submit-btn" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition font-bangla">
                    Login with Password
                </button>
            </form>
        </div>
        
        <!-- OTP Login Form -->
        <div id="otp-login" class="login-method {{ $errors->has('phone') || $errors->has('password') ? 'hidden' : '' }}">
            <div id="phone-step">
                <form id="send-otp-form">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2 font-bangla">ফোন নম্বর</label>
                        <input type="tel" name="phone" id="phone" required autofocus 
                               placeholder="01XXXXXXXXX" 
                               autocomplete="tel"
                               inputmode="numeric"
                               class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla">
                        <p class="mt-1 text-xs text-neutral-500 font-bangla">আপনার ১১ সংখ্যার মোবাইল নম্বর দিন</p>
                        <div id="phone-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                    
                    <button type="submit" id="send-otp-btn" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition font-bangla">
                        OTP পাঠান
                    </button>
                </form>
            </div>
        
            <div id="otp-step" class="hidden">
            <div class="mb-4">
                <p class="text-sm text-neutral-700 mb-4 font-bangla">
                    <span id="phone-display"></span> নম্বরে OTP পাঠানো হয়েছে
                </p>
            </div>
            
            <form id="verify-otp-form">
                @csrf
                <input type="hidden" name="phone" id="verify-phone">
                
                <div class="mb-4">
                    <label for="otp" class="block text-sm font-medium text-neutral-700 mb-2 font-bangla">OTP কোড</label>
                    <input type="text" name="otp" id="otp" required autofocus 
                           placeholder="000000" maxlength="6" 
                           autocomplete="one-time-code"
                           inputmode="numeric"
                           class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-center text-2xl tracking-widest font-mono">
                    <div id="otp-error" class="mt-1 text-sm text-red-600 hidden"></div>
                </div>
                
                <button type="submit" id="verify-otp-btn" class="w-full bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-light transition font-bangla mb-3">
                    যাচাই করুন
                </button>
                
                <button type="button" id="resend-otp-btn" class="w-full text-primary hover:underline text-sm font-bangla" disabled>
                    <span id="resend-text">পুনরায় OTP পাঠান (৬০ সেকেন্ড অপেক্ষা করুন)</span>
                </button>
            </form>
            </div>
        </div>
        
        <p class="mt-6 text-center text-sm text-neutral-600 font-bangla">
            একাউন্ট নেই? <a href="{{ route('register') }}" class="text-primary hover:underline">রেজিস্টার করুন</a>
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
    
    // Initialize tab state based on errors
    const hasPasswordErrors = {{ $errors->has('phone') || $errors->has('password') ? 'true' : 'false' }};
    if (hasPasswordErrors) {
        passwordTab.classList.add('border-b-2', 'border-primary', 'text-primary');
        passwordTab.classList.remove('text-neutral-600');
        otpTab.classList.remove('border-b-2', 'border-primary', 'text-primary');
        otpTab.classList.add('text-neutral-600');
        passwordLogin.classList.remove('hidden');
        otpLogin.classList.add('hidden');
    }
    
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
    
    // OTP Login elements
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
    
    // Password login elements
    const passwordForm = document.getElementById('password-form');
    const passwordPhoneInput = document.getElementById('password-phone');
    
    let resendTimer = null;
    let resendCountdown = 60;
    let otpAbortController = null;

    // Try to get phone number from device (if supported)
    if ('credentials' in navigator && 'get' in navigator.credentials) {
        navigator.credentials.get({ 
            password: true,
            mediation: 'silent'
        }).then(credential => {
            if (credential && credential.id) {
                // Some browsers may store phone numbers
                // This is a fallback, primary method is autocomplete="tel"
            }
        }).catch(() => {
            // Ignore errors - feature not available
        });
    }

    // Format phone input
    phoneInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
    
    // Format password phone input
    passwordPhoneInput.addEventListener('input', function(e) {
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
            showError(phoneError, 'সঠিক ফোন নম্বর দিন');
            return;
        }

        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = 'পাঠানো হচ্ছে...';
        clearError(phoneError);

        try {
            const response = await fetch('{{ route("login.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
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
                // If user doesn't exist, redirect to register with phone number
                if (data.redirect_to_register && data.phone) {
                    // Format phone for display (remove country code if present)
                    let displayPhone = data.phone;
                    if (displayPhone.startsWith('880')) {
                        displayPhone = '0' + displayPhone.substring(3);
                    }
                    window.location.href = '{{ route("register") }}?phone=' + encodeURIComponent(displayPhone);
                    return;
                }
                
                // If password login is required, automatically switch to password tab
                if (data.use_password_login) {
                    // Switch to password tab
                    passwordTab.click();
                    // Set phone number
                    passwordPhoneInput.value = phone;
                    passwordPhoneInput.focus();
                    return;
                }
                
                showError(phoneError, data.message || 'OTP পাঠাতে ব্যর্থ হয়েছে');
                sendOtpBtn.disabled = false;
                sendOtpBtn.textContent = 'OTP পাঠান';
            }
        } catch (error) {
            showError(phoneError, 'একটি ত্রুটি হয়েছে। দয়া করে আবার চেষ্টা করুন।');
            sendOtpBtn.disabled = false;
            sendOtpBtn.textContent = 'OTP পাঠান';
        }
    });

    // Verify OTP
    verifyOtpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const phone = verifyPhoneInput.value;
        const otp = otpInput.value.trim();

        if (otp.length !== 6) {
            showError(otpError, 'সঠিক OTP কোড দিন');
            return;
        }

        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'যাচাই করা হচ্ছে...';
        clearError(otpError);

        try {
            const response = await fetch('{{ route("login.verify-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#verify-otp-form input[name="_token"]').value
                },
                body: JSON.stringify({ phone: phone, otp: otp })
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect || '{{ route("home") }}';
            } else {
                showError(otpError, data.message || 'ভুল OTP কোড');
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'যাচাই করুন';
                otpInput.value = '';
                otpInput.focus();
            }
        } catch (error) {
            showError(otpError, 'একটি ত্রুটি হয়েছে। দয়া করে আবার চেষ্টা করুন।');
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.textContent = 'যাচাই করুন';
        }
    });

    // Resend OTP
    resendOtpBtn.addEventListener('click', async function() {
        if (resendOtpBtn.disabled) return;

        const phone = verifyPhoneInput.value;
        resendOtpBtn.disabled = true;

        try {
            const response = await fetch('{{ route("login.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
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
                showError(otpError, data.message || 'OTP পাঠাতে ব্যর্থ হয়েছে');
                resendOtpBtn.disabled = false;
            }
        } catch (error) {
            showError(otpError, 'একটি ত্রুটি হয়েছে। দয়া করে আবার চেষ্টা করুন।');
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
                resendText.textContent = `পুনরায় OTP পাঠান (${resendCountdown} সেকেন্ড অপেক্ষা করুন)`;
            } else {
                clearInterval(resendTimer);
                resendOtpBtn.disabled = false;
                resendText.textContent = 'পুনরায় OTP পাঠান';
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
        // Cancel any existing OTP detection
        if (otpAbortController) {
            otpAbortController.abort();
        }

        // Check if Web OTP API is supported
        if ('OTPCredential' in window) {
            otpAbortController = new AbortController();
            
            // Get the current domain for SMS format
            const domain = window.location.hostname;
            
            navigator.credentials.get({
                otp: { transport: ['sms'] },
                signal: otpAbortController.signal
            }).then(otp => {
                if (otp && otp.code) {
                    // Auto-fill OTP
                    otpInput.value = otp.code;
                    // Auto-submit if OTP is complete
                    if (otp.code.length === 6) {
                        verifyOtpForm.dispatchEvent(new Event('submit'));
                    }
                }
            }).catch(err => {
                // User cancelled or feature not available
                // Silently fail - user can manually enter OTP
                console.log('OTP auto-detection not available:', err);
            });
        }
    }

    // Stop OTP detection when form is submitted or page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && otpAbortController) {
            otpAbortController.abort();
        }
    });

    // Clean up on page unload
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
