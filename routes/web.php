<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Sponsor\DashboardController as SponsorDashboardController;
use App\Http\Middleware\TrackReferral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Public routes with referral tracking
Route::middleware([TrackReferral::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
    
    // Order routes
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');
});

// Authentication routes (with referral tracking, guest only)
Route::middleware(['guest', TrackReferral::class])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login/check-method', [LoginController::class, 'checkLoginMethod'])->name('login.check-method');
    Route::post('/login/send-otp', [LoginController::class, 'sendOtp'])->name('login.send-otp');
    Route::post('/login/verify-otp', [LoginController::class, 'verifyOtp'])->name('login.verify-otp');
    Route::post('/login', [LoginController::class, 'login']); // Legacy password-based login
    
    // Admin login routes
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login/check-method', [LoginController::class, 'checkAdminLoginMethod'])->name('admin.login.check-method');
    Route::post('/admin/login', [LoginController::class, 'adminLoginPassword'])->name('admin.login.password');
    Route::post('/admin/login/send-otp', [LoginController::class, 'adminSendOtp'])->name('admin.login.send-otp');
    Route::post('/admin/login/verify-otp', [LoginController::class, 'adminVerifyOtp'])->name('admin.login.verify-otp');
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register/send-otp', [RegisterController::class, 'sendOtp'])->name('register.send-otp');
    Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.verify-otp');
    
    // Password reset routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendResetOtp'])->name('password.send-otp');
    Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyResetOtp'])->name('password.verify-otp');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// Logout (requires auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::middleware(['admin', 'require.password.setup'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/products/{product}/builder', [AdminProductController::class, 'builder'])->name('products.builder');
        Route::resource('products', AdminProductController::class);
        Route::post('/upload-image', [\App\Http\Controllers\Admin\ImageUploadController::class, 'upload'])->name('upload.image');
        Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index');
        Route::get('/categories/create', [AdminDashboardController::class, 'createCategory'])->name('categories.create');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categories/{category}/edit', [AdminDashboardController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categories/{category}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');
        
        Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}/edit', [AdminDashboardController::class, 'editOrder'])->name('orders.edit');
        Route::put('/orders/{order}', [AdminDashboardController::class, 'updateOrder'])->name('orders.update');
        Route::put('/orders/{order}/status', [AdminDashboardController::class, 'updateOrderStatus'])->name('orders.updateStatus');
        Route::get('/orders/{order}', [AdminDashboardController::class, 'showOrder'])->name('orders.show');
        
        Route::get('/sponsors', [AdminDashboardController::class, 'sponsors'])->name('sponsors.index');
        Route::get('/sponsors/create', [AdminDashboardController::class, 'createSponsor'])->name('sponsors.create');
        Route::post('/sponsors', [AdminDashboardController::class, 'storeSponsor'])->name('sponsors.store');
        Route::get('/sponsors/{sponsor}', [AdminDashboardController::class, 'showSponsor'])->name('sponsors.show');
        Route::get('/sponsors/{sponsor}/edit', [AdminDashboardController::class, 'editSponsor'])->name('sponsors.edit');
        Route::put('/sponsors/{sponsor}', [AdminDashboardController::class, 'updateSponsor'])->name('sponsors.update');
        Route::delete('/sponsors/{sponsor}', [AdminDashboardController::class, 'destroySponsor'])->name('sponsors.destroy');
        
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminDashboardController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy');
        
        Route::get('/reports/sales', [AdminDashboardController::class, 'salesReport'])->name('reports.sales');
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');

        // Withdrawals management
        Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show');
        Route::put('/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'update'])->name('withdrawals.update');
        
        // Admin profile routes
        Route::get('/profile', [AdminDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [AdminDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/profile/change-password', [AdminDashboardController::class, 'showChangePasswordForm'])->name('profile.change-password');
        Route::put('/profile/change-password', [AdminDashboardController::class, 'updatePassword'])->name('profile.update-password');
    });
    
    // Sponsor/Affiliate routes
    Route::middleware(['sponsor', 'require.password.setup'])->prefix('sponsor')->name('sponsor.')->group(function () {
        Route::get('/dashboard', [SponsorDashboardController::class, 'index'])->name('dashboard');

        // Earnings & withdrawals
        Route::get('/earnings', [\App\Http\Controllers\Sponsor\EarningController::class, 'index'])->name('earnings.index');
        Route::get('/withdrawals', [\App\Http\Controllers\Sponsor\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/create', [\App\Http\Controllers\Sponsor\WithdrawalController::class, 'create'])->name('withdrawals.create');
        Route::post('/withdrawals', [\App\Http\Controllers\Sponsor\WithdrawalController::class, 'store'])->name('withdrawals.store');
        Route::match(['get', 'post'], '/withdrawal-methods', [\App\Http\Controllers\Sponsor\WithdrawalController::class, 'methods'])->name('withdrawal-methods');
        Route::post('/withdrawal-methods/{methodKey}/default', [\App\Http\Controllers\Sponsor\WithdrawalController::class, 'setDefaultMethod'])->name('withdrawal-methods.default');
        Route::delete('/withdrawal-methods/{methodKey}', [\App\Http\Controllers\Sponsor\WithdrawalController::class, 'deleteMethod'])->name('withdrawal-methods.delete');

        // Orders (my vs referral)
        Route::get('/orders', fn () => redirect()->route('sponsor.orders.referral-orders'))->name('orders.index'); // legacy
        Route::get('/orders/my-orders', [SponsorDashboardController::class, 'myOrders'])->name('orders.my-orders');
        Route::get('/orders/referral-orders', [SponsorDashboardController::class, 'referralOrders'])->name('orders.referral-orders');

        Route::get('/users', [SponsorDashboardController::class, 'referrals'])->name('users.index');
        Route::get('/users/create', [SponsorDashboardController::class, 'createUser'])->name('users.create');
        Route::post('/users', [SponsorDashboardController::class, 'addUser'])->name('users.store');
        Route::get('/users/{referral}', [SponsorDashboardController::class, 'showReferral'])->name('users.show');
        Route::get('/users/{referral}/edit', [SponsorDashboardController::class, 'editReferral'])->name('users.edit');
        Route::put('/users/{referral}', [SponsorDashboardController::class, 'updateReferral'])->name('users.update');
        Route::get('/profile/edit', [SponsorDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [SponsorDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [SponsorDashboardController::class, 'updatePassword'])->name('profile.update-password');
    });
});

Route::get('/login-as-user/{user}', function (User $user) {
    Auth::login($user);
    return redirect()->route('home');
});
