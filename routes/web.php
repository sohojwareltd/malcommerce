<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Sponsor\DashboardController as SponsorDashboardController;
use App\Http\Middleware\TrackReferral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// SEO / Feed routes (no auth, no referral - for crawlers and feeds)
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/feed/products.xml', [SeoController::class, 'productsXml'])->name('feed.products');

// Public routes with referral tracking
Route::middleware([TrackReferral::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/videos', [\App\Http\Controllers\VideoController::class, 'index'])->name('videos.index');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
    
    // Order routes
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');
    
    // Payment routes
    Route::match(['get', 'post'], '/payment/bkash/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateBkash'])->name('payment.bkash.initiate');
    Route::get('/payment/bkash/callback', [\App\Http\Controllers\PaymentController::class, 'bkashCallback'])->name('payment.bkash.callback');
    Route::get('/payment/bkash/cancel/{orderId}', [\App\Http\Controllers\PaymentController::class, 'cancelPayment'])->name('payment.bkash.cancel');
    Route::post('/payment/check-status', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('payment.check-status');
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
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('can:dashboard.view');
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index')->middleware('can:products.viewAny');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create')->middleware('can:products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store')->middleware('can:products.create');
        Route::get('/products/{product}/builder', [AdminProductController::class, 'builder'])->name('products.builder')->middleware('can:products.builder');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit')->middleware('can:products.update');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update')->middleware('can:products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy')->middleware('can:products.delete');
        Route::post('/products/{product}/restore', [AdminProductController::class, 'restore'])->name('products.restore')->middleware('can:products.restore');
        Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show')->middleware('can:products.view');
        Route::post('/upload-image', [\App\Http\Controllers\Admin\ImageUploadController::class, 'upload'])->name('upload.image')->middleware('can:products.create');
        Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index')->middleware('can:categories.viewAny');
        Route::get('/categories/create', [AdminDashboardController::class, 'createCategory'])->name('categories.create')->middleware('can:categories.create');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store')->middleware('can:categories.create');
        Route::get('/categories/{category}/edit', [AdminDashboardController::class, 'editCategory'])->name('categories.edit')->middleware('can:categories.update');
        Route::put('/categories/{category}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update')->middleware('can:categories.update');
        Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy')->middleware('can:categories.delete');
        Route::post('/categories/{category}/restore', [AdminDashboardController::class, 'restoreCategory'])->name('categories.restore')->middleware('can:categories.restore');
        
        Route::get('/videos', [\App\Http\Controllers\Admin\VideoController::class, 'index'])->name('videos.index')->middleware('can:videos.viewAny');
        Route::get('/videos/create', [\App\Http\Controllers\Admin\VideoController::class, 'create'])->name('videos.create')->middleware('can:videos.create');
        Route::post('/videos', [\App\Http\Controllers\Admin\VideoController::class, 'store'])->name('videos.store')->middleware('can:videos.create');
        Route::get('/videos/{video}/edit', [\App\Http\Controllers\Admin\VideoController::class, 'edit'])->name('videos.edit')->middleware('can:videos.update');
        Route::put('/videos/{video}', [\App\Http\Controllers\Admin\VideoController::class, 'update'])->name('videos.update')->middleware('can:videos.update');
        Route::delete('/videos/{video}', [\App\Http\Controllers\Admin\VideoController::class, 'destroy'])->name('videos.destroy')->middleware('can:videos.delete');
        Route::post('/videos/{video}/restore', [\App\Http\Controllers\Admin\VideoController::class, 'restore'])->name('videos.restore')->middleware('can:videos.restore');
        Route::get('/videos/{video}', [\App\Http\Controllers\Admin\VideoController::class, 'show'])->name('videos.show')->middleware('can:videos.view');
        
        Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index')->middleware('can:orders.viewAny');
        Route::post('/orders/bulk-delete', [AdminDashboardController::class, 'bulkDeleteOrders'])->name('orders.bulk-delete')->middleware('can:orders.bulkDelete');
        Route::post('/orders/bulk-ship', [AdminDashboardController::class, 'bulkMarkShipped'])->name('orders.bulk-ship')->middleware('can:orders.updateStatus');
        Route::get('/orders/{order}/edit', [AdminDashboardController::class, 'editOrder'])->name('orders.edit')->middleware('can:orders.update');
        Route::put('/orders/{order}', [AdminDashboardController::class, 'updateOrder'])->name('orders.update')->middleware('can:orders.update');
        Route::put('/orders/{order}/status', [AdminDashboardController::class, 'updateOrderStatus'])->name('orders.updateStatus')->middleware('can:orders.updateStatus');
        Route::post('/orders/{order}/steadfast-parcel', [AdminDashboardController::class, 'createSteadfastParcel'])->name('orders.steadfast.parcel')->middleware('can:orders.update');
        Route::post('/orders/{order}/steadfast-refresh', [AdminDashboardController::class, 'refreshSteadfastStatus'])->name('orders.steadfast.refresh')->middleware('can:orders.update');
        Route::delete('/orders/{order}/steadfast', [AdminDashboardController::class, 'removeSteadfastInfo'])->name('orders.steadfast.remove')->middleware('can:orders.update');
        Route::delete('/orders/{order}', [AdminDashboardController::class, 'destroyOrder'])->name('orders.destroy')->middleware('can:orders.delete');
        Route::post('/orders/{order}/restore', [AdminDashboardController::class, 'restoreOrder'])->name('orders.restore')->middleware('can:orders.restore');
        Route::get('/orders/{order}', [AdminDashboardController::class, 'showOrder'])->name('orders.show')->middleware('can:orders.view');
        
        Route::get('/sponsors', [AdminDashboardController::class, 'sponsors'])->name('sponsors.index')->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/create', [AdminDashboardController::class, 'createSponsor'])->name('sponsors.create')->middleware('can:sponsors.create');
        Route::post('/sponsors', [AdminDashboardController::class, 'storeSponsor'])->name('sponsors.store')->middleware('can:sponsors.create');
        Route::get('/sponsors/{sponsor}', [AdminDashboardController::class, 'showSponsor'])->name('sponsors.show')->middleware('can:sponsors.view');
        Route::get('/sponsors/{sponsor}/edit', [AdminDashboardController::class, 'editSponsor'])->name('sponsors.edit')->middleware('can:sponsors.update');
        Route::put('/sponsors/{sponsor}', [AdminDashboardController::class, 'updateSponsor'])->name('sponsors.update')->middleware('can:sponsors.update');
        Route::delete('/sponsors/{sponsor}', [AdminDashboardController::class, 'destroySponsor'])->name('sponsors.destroy')->middleware('can:sponsors.delete');
        Route::post('/sponsors/{sponsor}/restore', [AdminDashboardController::class, 'restoreSponsor'])->name('sponsors.restore')->middleware('can:sponsors.restore');
        
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index')->middleware('can:users.viewAny');
        Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('users.create')->middleware('can:users.create');
        Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store')->middleware('can:users.create');
        Route::get('/users/{user}/edit', [AdminDashboardController::class, 'editUser'])->name('users.edit')->middleware('can:users.update');
        Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update')->middleware('can:users.update');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy')->middleware('can:users.delete');
        Route::post('/users/{user}/restore', [AdminDashboardController::class, 'restoreUser'])->name('users.restore')->middleware('can:users.restore');
        
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index')->middleware('can:roles.viewAny');
        Route::get('/roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create')->middleware('can:roles.create');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store')->middleware('can:roles.create');
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit')->middleware('can:roles.update');
        Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update')->middleware('can:roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy')->middleware('can:roles.delete');
        
        Route::get('/expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('expenses.index')->middleware('can:expenses.viewAny');
        Route::get('/expenses/export', [\App\Http\Controllers\Admin\ExpenseController::class, 'export'])->name('expenses.export')->middleware('can:expenses.viewAny');
        Route::get('/expenses/create', [\App\Http\Controllers\Admin\ExpenseController::class, 'create'])->name('expenses.create')->middleware('can:expenses.create');
        Route::post('/expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('expenses.store')->middleware('can:expenses.create');
        Route::get('/expenses/{expense}/edit', [\App\Http\Controllers\Admin\ExpenseController::class, 'edit'])->name('expenses.edit')->middleware('can:expenses.update');
        Route::put('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'update'])->name('expenses.update')->middleware('can:expenses.update');
        Route::delete('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('can:expenses.delete');

        Route::get('/reports/sales', [AdminDashboardController::class, 'salesReport'])->name('reports.sales')->middleware('can:reports.sales');
        Route::get('/reports/sales/export', [AdminDashboardController::class, 'exportSalesReport'])->name('reports.sales.export')->middleware('can:reports.sales');
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings')->middleware('can:settings.view');
        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update')->middleware('can:settings.update');
        Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index')->middleware('can:withdrawals.viewAny');
        Route::get('/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show')->middleware('can:withdrawals.view');
        Route::put('/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'update'])->name('withdrawals.update')->middleware('can:withdrawals.update');
        Route::get('/profile', [AdminDashboardController::class, 'editProfile'])->name('profile.edit')->middleware('can:profile.view');
        Route::put('/profile', [AdminDashboardController::class, 'updateProfile'])->name('profile.update')->middleware('can:profile.update');
        Route::get('/profile/change-password', [AdminDashboardController::class, 'showChangePasswordForm'])->name('profile.change-password')->middleware('can:profile.changePassword');
        Route::put('/profile/change-password', [AdminDashboardController::class, 'updatePassword'])->name('profile.update-password')->middleware('can:profile.changePassword');
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
