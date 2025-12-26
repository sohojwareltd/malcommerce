@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center mb-6 font-bangla">লগইন করুন</h2>
        
        <div id="phone-step">
            <form id="send-otp-form">
                @csrf
                
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2 font-bangla">ফোন নম্বর</label>
                    <input type="tel" name="phone" id="phone" required autofocus 
                           placeholder="01XXXXXXXXX" 
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
        
        <p class="mt-6 text-center text-sm text-neutral-600 font-bangla">
            একাউন্ট নেই? <a href="{{ route('register') }}" class="text-primary hover:underline">রেজিস্টার করুন</a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
            } else {
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
});
</script>
@endsection
