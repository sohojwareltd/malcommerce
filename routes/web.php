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
use App\Http\Controllers\Admin\PurchaseController as AdminPurchaseController;
use App\Http\Controllers\Sponsor\DashboardController as SponsorDashboardController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Middleware\TrackReferral;

// Webhooks (no auth, no CSRF - external POST)
Route::post('/webhooks/steadfast', \App\Http\Controllers\SteadfastWebhookController::class)->name('webhooks.steadfast');
Route::get('/assets/bkash-logo', function () {
    return response()->file(resource_path('views/bkash.png'));
})->name('assets.bkash.logo');

// SEO / Feed routes (no auth, no referral - for crawlers and feeds)
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/feed/products.xml', [SeoController::class, 'productsXml'])->name('feed.products');

// Public routes with referral tracking
Route::middleware([TrackReferral::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/videos', [\App\Http\Controllers\VideoController::class, 'index'])->name('videos.index');
    Route::get('/courses', [\App\Http\Controllers\DigitalCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course:slug}', [\App\Http\Controllers\DigitalCourseController::class, 'show'])->name('courses.show');
    Route::get('/exams', [\App\Http\Controllers\ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam:slug}', [\App\Http\Controllers\ExamController::class, 'show'])->name('exams.show');
    Route::get('/certificate/verify', [\App\Http\Controllers\CertificateController::class, 'verify'])->name('certificates.verify');
    Route::get('/student-documents/verify/{serialNumber}', \App\Http\Controllers\StudentDocumentVerifyController::class)->name('student-documents.verify');
    Route::get('/courses/{course:slug}/lessons/{lesson}', [\App\Http\Controllers\DigitalCourseController::class, 'watchLesson'])->name('courses.lessons.watch');
    Route::post('/courses/{course:slug}/checkout', [\App\Http\Controllers\DigitalCourseOrderController::class, 'store'])->name('courses.checkout');
    Route::get('/course-orders/success/{orderNumber}', [\App\Http\Controllers\DigitalCourseOrderController::class, 'success'])->name('course-orders.success');
    Route::match(['get', 'post'], '/payment/course-bkash/initiate', [\App\Http\Controllers\DigitalCoursePaymentController::class, 'initiateBkash'])->name('payment.course-bkash.initiate');
    Route::get('/payment/course-bkash/callback', [\App\Http\Controllers\DigitalCoursePaymentController::class, 'bkashCallback'])->name('payment.course-bkash.callback');
    Route::get('/payment/course-bkash/cancel/{orderId}', [\App\Http\Controllers\DigitalCoursePaymentController::class, 'cancelPayment'])->name('payment.course-bkash.cancel');
    Route::post('/payment/course-check-status', [\App\Http\Controllers\DigitalCoursePaymentController::class, 'checkStatus'])->name('payment.course-check-status');
    Route::post('/exams/{exam:slug}/checkout', [\App\Http\Controllers\ExamOrderController::class, 'store'])->name('exams.checkout');
    Route::get('/exam-orders/success/{orderNumber}', [\App\Http\Controllers\ExamOrderController::class, 'success'])->name('exam-orders.success');
    Route::match(['get', 'post'], '/payment/exam-bkash/initiate', [\App\Http\Controllers\ExamPaymentController::class, 'initiateBkash'])->name('payment.exam-bkash.initiate');
    Route::get('/payment/exam-bkash/callback', [\App\Http\Controllers\ExamPaymentController::class, 'bkashCallback'])->name('payment.exam-bkash.callback');
    Route::get('/payment/exam-bkash/cancel/{orderId}', [\App\Http\Controllers\ExamPaymentController::class, 'cancelPayment'])->name('payment.exam-bkash.cancel');
    Route::post('/payment/exam-check-status', [\App\Http\Controllers\ExamPaymentController::class, 'checkStatus'])->name('payment.exam-check-status');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/jobs', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{jobCircular}', [\App\Http\Controllers\JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{jobCircular}/apply', [\App\Http\Controllers\JobController::class, 'apply'])->name('jobs.apply');
    Route::get('/workshops', [\App\Http\Controllers\WorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshopSeminar}', [\App\Http\Controllers\WorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshopSeminar}/enroll', [\App\Http\Controllers\WorkshopController::class, 'enroll'])->name('workshops.enroll');
    
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

// Logout and digital product access (requires auth)
Route::middleware('auth')->group(function () {
    Route::post('/impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/my-courses', [\App\Http\Controllers\MyCourseController::class, 'index'])->name('my-courses.index');
    Route::redirect('/purchased-courses', '/my-courses')->name('purchased-courses.index');
    Route::get('/my-courses/{course:slug}', [\App\Http\Controllers\MyCourseController::class, 'show'])->name('my-courses.show');

    Route::post('/exams/{exam:slug}/redeem-code', [\App\Http\Controllers\ExamCodeController::class, 'redeem'])->name('exams.redeem-code');
    Route::post('/exams/{exam:slug}/start', [\App\Http\Controllers\ExamSessionController::class, 'start'])->name('exams.start');
    Route::get('/exam-attempts/{attempt}', [\App\Http\Controllers\ExamSessionController::class, 'take'])->name('exams.take');
    Route::post('/exam-attempts/{attempt}/submit', [\App\Http\Controllers\ExamSessionController::class, 'submit'])->name('exams.submit');
    Route::get('/exam-attempts/{attempt}/result', [\App\Http\Controllers\ExamSessionController::class, 'result'])->name('exams.result');
    Route::get('/my-certificates', [\App\Http\Controllers\ExamSessionController::class, 'myCertificates'])->name('my-certificates.index');
    Route::get('/certificates/{certificate}', [\App\Http\Controllers\CertificateController::class, 'show'])->name('certificates.show');

    // Admin routes
    Route::middleware(['admin', 'require.password.setup'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('can:dashboard.view');
        Route::get('/purchases', [AdminPurchaseController::class, 'index'])->name('purchases.index')->middleware('can:dashboard.view');
        Route::get('/purchases/print', [AdminPurchaseController::class, 'printReport'])->name('purchases.print')->middleware('can:dashboard.view');
        Route::get('/purchases/create', [AdminPurchaseController::class, 'create'])->name('purchases.create')->middleware('can:dashboard.view');
        Route::post('/purchases', [AdminPurchaseController::class, 'store'])->name('purchases.store')->middleware('can:dashboard.view');
        Route::get('/purchases/{purchase}', [AdminPurchaseController::class, 'show'])->name('purchases.show')->middleware('can:dashboard.view');
        Route::patch('/purchases/{purchase}', [AdminPurchaseController::class, 'updateStatus'])->name('purchases.update-status')->middleware('can:dashboard.view');
        Route::delete('/purchases/{purchase}', [AdminPurchaseController::class, 'destroy'])->name('purchases.destroy')->middleware('can:dashboard.view');
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index')->middleware('can:products.viewAny');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create')->middleware('can:products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store')->middleware('can:products.create');
        Route::get('/products/{product}/builder', [AdminProductController::class, 'builder'])->name('products.builder')->middleware('can:products.builder');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit')->middleware('can:products.update');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update')->middleware('can:products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy')->middleware('can:products.delete');
        Route::post('/products/{product}/restore', [AdminProductController::class, 'restore'])->name('products.restore')->middleware('can:products.restore');
        Route::delete('/products/{product}/force', [AdminProductController::class, 'forceDestroy'])->name('products.force-delete')->middleware('can:products.forceDelete');
        Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show')->middleware('can:products.view');
        Route::post('/upload-image', [\App\Http\Controllers\Admin\ImageUploadController::class, 'upload'])->name('upload.image')->middleware('can:products.create');
        Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index')->middleware('can:categories.viewAny');
        Route::get('/categories/create', [AdminDashboardController::class, 'createCategory'])->name('categories.create')->middleware('can:categories.create');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store')->middleware('can:categories.create');
        Route::get('/categories/{category}/edit', [AdminDashboardController::class, 'editCategory'])->name('categories.edit')->middleware('can:categories.update');
        Route::put('/categories/{category}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update')->middleware('can:categories.update');
        Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy')->middleware('can:categories.delete');
        Route::post('/categories/{category}/restore', [AdminDashboardController::class, 'restoreCategory'])->name('categories.restore')->middleware('can:categories.restore');
        Route::delete('/categories/{category}/force', [AdminDashboardController::class, 'forceDestroyCategory'])->name('categories.force-delete')->middleware('can:categories.forceDelete');

        Route::get('/videos', [\App\Http\Controllers\Admin\VideoController::class, 'index'])->name('videos.index')->middleware('can:videos.viewAny');
        Route::get('/videos/create', [\App\Http\Controllers\Admin\VideoController::class, 'create'])->name('videos.create')->middleware('can:videos.create');
        Route::post('/videos', [\App\Http\Controllers\Admin\VideoController::class, 'store'])->name('videos.store')->middleware('can:videos.create');
        Route::get('/videos/{video}/edit', [\App\Http\Controllers\Admin\VideoController::class, 'edit'])->name('videos.edit')->middleware('can:videos.update');
        Route::put('/videos/{video}', [\App\Http\Controllers\Admin\VideoController::class, 'update'])->name('videos.update')->middleware('can:videos.update');
        Route::delete('/videos/{video}', [\App\Http\Controllers\Admin\VideoController::class, 'destroy'])->name('videos.destroy')->middleware('can:videos.delete');
        Route::post('/videos/{video}/restore', [\App\Http\Controllers\Admin\VideoController::class, 'restore'])->name('videos.restore')->middleware('can:videos.restore');
        Route::delete('/videos/{video}/force', [\App\Http\Controllers\Admin\VideoController::class, 'forceDestroy'])->name('videos.force-delete')->middleware('can:videos.forceDelete');
        Route::get('/videos/{video}', [\App\Http\Controllers\Admin\VideoController::class, 'show'])->name('videos.show')->middleware('can:videos.view');

        Route::get('/digital-courses', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'index'])->name('digital-courses.index')->middleware('can:digital_courses.viewAny');
        Route::get('/digital-courses/create', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'create'])->name('digital-courses.create')->middleware('can:digital_courses.create');
        Route::post('/digital-courses', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'store'])->name('digital-courses.store')->middleware('can:digital_courses.create');
        Route::get('/digital-courses/{digitalCourse}/builder', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'builder'])->name('digital-courses.builder')->middleware('can:digital_courses.update');
        Route::get('/digital-courses/{digitalCourse}/edit', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'edit'])->name('digital-courses.edit')->middleware('can:digital_courses.update');
        Route::put('/digital-courses/{digitalCourse}', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'update'])->name('digital-courses.update')->middleware('can:digital_courses.update');
        Route::delete('/digital-courses/{digitalCourse}', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'destroy'])->name('digital-courses.destroy')->middleware('can:digital_courses.delete');
        Route::post('/digital-courses/{digitalCourse}/restore', [\App\Http\Controllers\Admin\DigitalCourseController::class, 'restore'])->name('digital-courses.restore')->middleware('can:digital_courses.restore');

        Route::get('/digital-course-categories', [\App\Http\Controllers\Admin\DigitalCourseCategoryController::class, 'index'])->name('digital-course-categories.index')->middleware('can:digital_courses.viewAny');
        Route::get('/digital-course-categories/create', [\App\Http\Controllers\Admin\DigitalCourseCategoryController::class, 'create'])->name('digital-course-categories.create')->middleware('can:digital_courses.create');
        Route::post('/digital-course-categories', [\App\Http\Controllers\Admin\DigitalCourseCategoryController::class, 'store'])->name('digital-course-categories.store')->middleware('can:digital_courses.create');
        Route::get('/digital-course-categories/{digitalCourseCategory}/edit', [\App\Http\Controllers\Admin\DigitalCourseCategoryController::class, 'edit'])->name('digital-course-categories.edit')->middleware('can:digital_courses.update');
        Route::put('/digital-course-categories/{digitalCourseCategory}', [\App\Http\Controllers\Admin\DigitalCourseCategoryController::class, 'update'])->name('digital-course-categories.update')->middleware('can:digital_courses.update');
        Route::delete('/digital-course-categories/{digitalCourseCategory}', [\App\Http\Controllers\Admin\DigitalCourseCategoryController::class, 'destroy'])->name('digital-course-categories.destroy')->middleware('can:digital_courses.delete');

        Route::get('/digital-course-orders', [\App\Http\Controllers\Admin\DigitalCourseOrderController::class, 'index'])->name('digital-course-orders.index')->middleware('can:digital_courses.viewAny');
        Route::get('/digital-course-orders/{digitalCourseOrder}', [\App\Http\Controllers\Admin\DigitalCourseOrderController::class, 'show'])->name('digital-course-orders.show')->middleware('can:digital_courses.viewAny');
        Route::post('/digital-course-orders/{digitalCourseOrder}/mark-paid', [\App\Http\Controllers\Admin\DigitalCourseOrderController::class, 'markPaid'])->name('digital-course-orders.mark-paid')->middleware('can:digital_courses.update');

        Route::get('/exams', [\App\Http\Controllers\Admin\ExamController::class, 'index'])->name('exams.index')->middleware('can:exams.viewAny');
        Route::get('/exams/create', [\App\Http\Controllers\Admin\ExamController::class, 'create'])->name('exams.create')->middleware('can:exams.create');
        Route::post('/exams', [\App\Http\Controllers\Admin\ExamController::class, 'store'])->name('exams.store')->middleware('can:exams.create');
        Route::get('/exams/{exam}/edit', [\App\Http\Controllers\Admin\ExamController::class, 'edit'])->name('exams.edit')->middleware('can:exams.update');
        Route::put('/exams/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'update'])->name('exams.update')->middleware('can:exams.update');
        Route::delete('/exams/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'destroy'])->name('exams.destroy')->middleware('can:exams.delete');
        Route::post('/exams/{exam}/restore', [\App\Http\Controllers\Admin\ExamController::class, 'restore'])->name('exams.restore')->middleware('can:exams.restore');
        Route::post('/exams/{exam}/generate-codes', [\App\Http\Controllers\Admin\ExamController::class, 'generateCodes'])->name('exams.generate-codes')->middleware('can:exams.update');
        Route::get('/exam-orders', [\App\Http\Controllers\Admin\ExamOrderController::class, 'index'])->name('exam-orders.index')->middleware('can:exams.viewAny');
        Route::get('/exam-orders/{examOrder}', [\App\Http\Controllers\Admin\ExamOrderController::class, 'show'])->name('exam-orders.show')->middleware('can:exams.viewAny');
        Route::post('/exam-orders/{examOrder}/mark-paid', [\App\Http\Controllers\Admin\ExamOrderController::class, 'markPaid'])->name('exam-orders.mark-paid')->middleware('can:exams.update');
        Route::get('/exam-enrollments', [\App\Http\Controllers\Admin\ExamEnrollmentController::class, 'index'])->name('exam-enrollments.index')->middleware('can:exams.viewAny');
        Route::get('/exam-attempts', [\App\Http\Controllers\Admin\ExamEnrollmentController::class, 'attempts'])->name('exam-attempts.index')->middleware('can:exams.viewAny');
        Route::get('/exam-certificates', [\App\Http\Controllers\Admin\ExamEnrollmentController::class, 'certificates'])->name('exam-certificates.index')->middleware('can:exams.viewAny');
        Route::get('/exam-code-bans', [\App\Http\Controllers\Admin\ExamEnrollmentController::class, 'codeBans'])->name('exam-code-bans.index')->middleware('can:exams.viewAny');
        Route::post('/exam-code-bans/unban', [\App\Http\Controllers\Admin\ExamEnrollmentController::class, 'unbanCode'])->name('exam-code-bans.unban')->middleware('can:exams.update');
        Route::post('/exam-enrollments/grant', [\App\Http\Controllers\Admin\ExamEnrollmentController::class, 'grantAccess'])->name('exam-enrollments.grant')->middleware('can:exams.update');

        Route::get('/institutes', [\App\Http\Controllers\Admin\InstituteController::class, 'index'])->name('institutes.index')->middleware('can:institutes.viewAny');
        Route::get('/institutes/create', [\App\Http\Controllers\Admin\InstituteController::class, 'create'])->name('institutes.create')->middleware('can:institutes.create');
        Route::post('/institutes', [\App\Http\Controllers\Admin\InstituteController::class, 'store'])->name('institutes.store')->middleware('can:institutes.create');
        Route::get('/institutes/{institute}/edit', [\App\Http\Controllers\Admin\InstituteController::class, 'edit'])->name('institutes.edit')->middleware('can:institutes.update');
        Route::put('/institutes/{institute}', [\App\Http\Controllers\Admin\InstituteController::class, 'update'])->name('institutes.update')->middleware('can:institutes.update');
        Route::delete('/institutes/{institute}', [\App\Http\Controllers\Admin\InstituteController::class, 'destroy'])->name('institutes.destroy')->middleware('can:institutes.delete');
        Route::post('/institutes/{institute}/restore', [\App\Http\Controllers\Admin\InstituteController::class, 'restore'])->name('institutes.restore')->middleware('can:institutes.restore');

        Route::get('/student-courses', [\App\Http\Controllers\Admin\StudentCourseController::class, 'index'])->name('student-courses.index')->middleware('can:studentCourses.viewAny');
        Route::get('/student-courses/create', [\App\Http\Controllers\Admin\StudentCourseController::class, 'create'])->name('student-courses.create')->middleware('can:studentCourses.create');
        Route::post('/student-courses', [\App\Http\Controllers\Admin\StudentCourseController::class, 'store'])->name('student-courses.store')->middleware('can:studentCourses.create');
        Route::get('/student-courses/{studentCourse}/edit', [\App\Http\Controllers\Admin\StudentCourseController::class, 'edit'])->name('student-courses.edit')->middleware('can:studentCourses.update');
        Route::put('/student-courses/{studentCourse}', [\App\Http\Controllers\Admin\StudentCourseController::class, 'update'])->name('student-courses.update')->middleware('can:studentCourses.update');
        Route::delete('/student-courses/{studentCourse}', [\App\Http\Controllers\Admin\StudentCourseController::class, 'destroy'])->name('student-courses.destroy')->middleware('can:studentCourses.delete');

        Route::get('/students', [\App\Http\Controllers\Admin\StudentController::class, 'index'])->name('students.index')->middleware('can:students.viewAny');
        Route::get('/students/create', [\App\Http\Controllers\Admin\StudentController::class, 'create'])->name('students.create')->middleware('can:students.create');
        Route::post('/students', [\App\Http\Controllers\Admin\StudentController::class, 'store'])->name('students.store')->middleware('can:students.create');
        Route::get('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'show'])->name('students.show')->middleware('can:students.view');
        Route::get('/students/{student}/edit', [\App\Http\Controllers\Admin\StudentController::class, 'edit'])->name('students.edit')->middleware('can:students.update');
        Route::put('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'update'])->name('students.update')->middleware('can:students.update');
        Route::delete('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('students.destroy')->middleware('can:students.delete');
        Route::post('/students/{student}/restore', [\App\Http\Controllers\Admin\StudentController::class, 'restore'])->name('students.restore')->middleware('can:students.restore');
        Route::get('/students/{student}/documents/registration', [\App\Http\Controllers\Admin\StudentDocumentController::class, 'registration'])->name('students.documents.registration')->middleware('can:students.view');
        Route::get('/students/{student}/documents/admit', [\App\Http\Controllers\Admin\StudentDocumentController::class, 'admit'])->name('students.documents.admit')->middleware('can:students.view');
        Route::get('/students/{student}/documents/certificate', [\App\Http\Controllers\Admin\StudentDocumentController::class, 'certificate'])->name('students.documents.certificate')->middleware('can:students.view');
        Route::get('/students/{student}/documents/marksheet', [\App\Http\Controllers\Admin\StudentDocumentController::class, 'marksheet'])->name('students.documents.marksheet')->middleware('can:students.view');
        
        Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index')->middleware('can:orders.viewAny');
        Route::get('/orders/physical', [AdminDashboardController::class, 'ordersPhysical'])->name('orders.physical')->middleware('can:orders.viewAny');
        Route::get('/orders/digital', fn () => redirect()->route('admin.digital-course-orders.index'))->name('orders.digital')->middleware('can:orders.viewAny');
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
        Route::delete('/orders/{order}/force', [AdminDashboardController::class, 'forceDestroyOrder'])->name('orders.force-delete')->middleware('can:orders.forceDelete');
        Route::get('/orders/{order}', [AdminDashboardController::class, 'showOrder'])->name('orders.show')->middleware('can:orders.view');
        
        Route::resource('sponsor-levels', \App\Http\Controllers\Admin\SponsorLevelController::class)
            ->except(['show'])
            ->names('sponsor-levels');

        Route::post('/sponsors/{sponsor}/login-as', [ImpersonateController::class, 'loginAsSponsor'])
            ->name('sponsors.login-as')
            ->whereNumber('sponsor')
            ->middleware('can:sponsors.view');

        Route::get('/sponsors', [AdminDashboardController::class, 'sponsors'])->name('sponsors.index')->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/by-balance', [AdminDashboardController::class, 'sponsorsByBalance'])->name('sponsors.by-balance')->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/pending-earnings', [AdminDashboardController::class, 'sponsorPendingEarnings'])->name('sponsors.pending-earnings')->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/leaderboard', [AdminDashboardController::class, 'sponsorLeaderboard'])->name('sponsors.leaderboard')->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/print/partners', [AdminDashboardController::class, 'sponsorsPrintPartners'])->name('sponsors.print.partners')->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/print/leaderboard', [AdminDashboardController::class, 'sponsorsPrintLeaderboard'])->name('sponsors.print.leaderboard')->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/create', [AdminDashboardController::class, 'createSponsor'])->name('sponsors.create')->middleware('can:sponsors.create');
        Route::post('/sponsors', [AdminDashboardController::class, 'storeSponsor'])->name('sponsors.store')->middleware('can:sponsors.create');
        Route::post('/sponsors/bulk-set-referrer', [AdminDashboardController::class, 'bulkSetSponsorReferrer'])->name('sponsors.bulk-set-referrer')->middleware('can:sponsors.update');
        Route::post('/sponsors/bulk-set-level', [AdminDashboardController::class, 'bulkSetSponsorLevel'])->name('sponsors.bulk-set-level')->middleware('can:sponsors.update');
        // Avoid GET bulk routes matching {sponsor} = route segment (404); bulk is POST-only from the index form.
        Route::get('/sponsors/bulk-set-referrer', fn () => redirect()->route('admin.sponsors.index'))->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/bulk-set-level', fn () => redirect()->route('admin.sponsors.index'))->middleware('can:sponsors.viewAny');
        Route::get('/sponsors/{sponsor}', [AdminDashboardController::class, 'showSponsor'])->name('sponsors.show')->whereNumber('sponsor')->middleware('can:sponsors.view');
        Route::get('/sponsors/{sponsor}/edit', [AdminDashboardController::class, 'editSponsor'])->name('sponsors.edit')->whereNumber('sponsor')->middleware('can:sponsors.update');
        Route::get('/sponsors/{sponsor}/withdrawals', [AdminDashboardController::class, 'showSponsorWithdrawals'])->name('sponsors.withdrawals')->whereNumber('sponsor')->middleware('can:sponsors.view');
        Route::post('/sponsors/{sponsor}/withdrawals', [AdminDashboardController::class, 'storeSponsorWithdrawal'])->name('sponsors.withdrawals.store')->whereNumber('sponsor')->middleware('can:create,'.\App\Models\Withdrawal::class);
        Route::post('/sponsors/{sponsor}/balance', [AdminDashboardController::class, 'updateSponsorBalance'])->name('sponsors.balance.update')->whereNumber('sponsor')->middleware('can:sponsors.update');
        Route::post('/sponsors/{sponsor}/income', [AdminDashboardController::class, 'storeSponsorIncome'])->name('sponsors.income.store')->whereNumber('sponsor')->middleware('can:sponsors.update');
        Route::delete('/sponsors/{sponsor}/income/{income}', [AdminDashboardController::class, 'destroySponsorIncome'])->name('sponsors.income.destroy')->whereNumber('sponsor')->whereNumber('income')->middleware('can:sponsors.update');
        Route::delete('/sponsors/{sponsor}/earnings/{earning}', [AdminDashboardController::class, 'destroySponsorEarning'])->name('sponsors.earnings.destroy')->whereNumber('sponsor')->whereNumber('earning')->middleware('can:sponsors.update');
        Route::put('/sponsors/{sponsor}', [AdminDashboardController::class, 'updateSponsor'])->name('sponsors.update')->whereNumber('sponsor')->middleware('can:sponsors.update');
        Route::delete('/sponsors/{sponsor}', [AdminDashboardController::class, 'destroySponsor'])->name('sponsors.destroy')->whereNumber('sponsor')->middleware('can:sponsors.delete');
        Route::post('/sponsors/{sponsor}/restore', [AdminDashboardController::class, 'restoreSponsor'])->name('sponsors.restore')->whereNumber('sponsor')->middleware('can:sponsors.restore');
        Route::delete('/sponsors/{sponsor}/force', [AdminDashboardController::class, 'forceDestroySponsor'])->name('sponsors.force-delete')->whereNumber('sponsor')->middleware('can:sponsors.forceDelete');

        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index')->middleware('can:users.viewAny');
        Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('users.create')->middleware('can:users.create');
        Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store')->middleware('can:users.create');
        Route::get('/users/{user}/edit', [AdminDashboardController::class, 'editUser'])->name('users.edit')->middleware('can:users.update');
        Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update')->middleware('can:users.update');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy')->middleware('can:users.delete');
        Route::post('/users/{user}/restore', [AdminDashboardController::class, 'restoreUser'])->name('users.restore')->middleware('can:users.restore');
        Route::delete('/users/{user}/force', [AdminDashboardController::class, 'forceDestroyUser'])->name('users.force-delete')->middleware('can:users.forceDelete');

        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index')->middleware('can:roles.viewAny');
        Route::get('/roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create')->middleware('can:roles.create');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store')->middleware('can:roles.create');
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit')->middleware('can:roles.update');
        Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update')->middleware('can:roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy')->middleware('can:roles.delete');
        
        Route::resource('job-circulars', \App\Http\Controllers\Admin\JobCircularController::class)->names('job-circulars');
        Route::get('/job-applications', [\App\Http\Controllers\Admin\JobApplicationController::class, 'index'])->name('job-applications.index')->middleware('can:jobApplications.viewAny');
        Route::get('/job-applications/export', [\App\Http\Controllers\Admin\JobApplicationController::class, 'export'])->name('job-applications.export')->middleware('can:jobApplications.viewAny');
        Route::get('/job-applications/{jobApplication}', [\App\Http\Controllers\Admin\JobApplicationController::class, 'show'])->name('job-applications.show')->middleware('can:jobApplications.view');
        Route::patch('/job-applications/{jobApplication}/status', [\App\Http\Controllers\Admin\JobApplicationController::class, 'updateStatus'])->name('job-applications.update-status')->middleware('can:jobApplications.update');
        Route::patch('/job-applications/bulk-status', [\App\Http\Controllers\Admin\JobApplicationController::class, 'bulkUpdateStatus'])->name('job-applications.bulk-update-status')->middleware('can:jobApplications.update');
        Route::delete('/job-applications/{jobApplication}', [\App\Http\Controllers\Admin\JobApplicationController::class, 'destroy'])->name('job-applications.destroy')->middleware('can:jobApplications.delete');
        Route::resource('venues', \App\Http\Controllers\Admin\VenueController::class)->names('venues')->middleware('can:workshopSeminars.viewAny');
        Route::resource('trades', \App\Http\Controllers\Admin\TradeController::class)->names('trades')->middleware('can:workshopSeminars.viewAny');
        Route::resource('workshop-seminars', \App\Http\Controllers\Admin\WorkshopSeminarController::class)->names('workshop-seminars');
        Route::get('/workshop-enrollments', [\App\Http\Controllers\Admin\WorkshopEnrollmentController::class, 'index'])->name('workshop-enrollments.index')->middleware('can:workshopEnrollments.viewAny');
        Route::get('/workshop-enrollments/export', [\App\Http\Controllers\Admin\WorkshopEnrollmentController::class, 'export'])->name('workshop-enrollments.export')->middleware('can:workshopEnrollments.viewAny');
        Route::get('/workshop-enrollments/{workshopEnrollment}', [\App\Http\Controllers\Admin\WorkshopEnrollmentController::class, 'show'])->name('workshop-enrollments.show')->middleware('can:workshopEnrollments.view');
        Route::patch('/workshop-enrollments/{workshopEnrollment}/status', [\App\Http\Controllers\Admin\WorkshopEnrollmentController::class, 'updateStatus'])->name('workshop-enrollments.update-status')->middleware('can:workshopEnrollments.update');
        Route::get('/expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('expenses.index')->middleware('can:expenses.viewAny');
        Route::get('/expenses/export', [\App\Http\Controllers\Admin\ExpenseController::class, 'export'])->name('expenses.export')->middleware('can:expenses.viewAny');
        Route::get('/expenses/create', [\App\Http\Controllers\Admin\ExpenseController::class, 'create'])->name('expenses.create')->middleware('can:expenses.create');
        Route::post('/expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('expenses.store')->middleware('can:expenses.create');
        Route::get('/expenses/{expense}/edit', [\App\Http\Controllers\Admin\ExpenseController::class, 'edit'])->name('expenses.edit')->middleware('can:expenses.update');
        Route::put('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'update'])->name('expenses.update')->middleware('can:expenses.update');
        Route::delete('/expenses/{expense}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('can:expenses.delete');

        Route::get('/expense-categories', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'index'])->name('expense-categories.index')->middleware('can:expenses.viewAny');
        Route::get('/expense-categories/create', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'create'])->name('expense-categories.create')->middleware('can:expenses.create');
        Route::post('/expense-categories', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'store'])->name('expense-categories.store')->middleware('can:expenses.create');
        Route::get('/expense-categories/{expenseCategory}/edit', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit')->middleware('can:expenses.update');
        Route::put('/expense-categories/{expenseCategory}', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'update'])->name('expense-categories.update')->middleware('can:expenses.update');
        Route::delete('/expense-categories/{expenseCategory}', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy')->middleware('can:expenses.delete');

        Route::get('/reports/sales', [AdminDashboardController::class, 'salesReport'])->name('reports.sales')->middleware('can:reports.sales');
        Route::get('/reports/sales/export', [AdminDashboardController::class, 'exportSalesReport'])->name('reports.sales.export')->middleware('can:reports.sales');
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings')->middleware('can:settings.view');
        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update')->middleware('can:settings.update');
        Route::get('/settings/sms-send', [\App\Http\Controllers\Admin\SmsSendController::class, 'index'])->name('settings.sms-send')->middleware('can:settings.smsSend');
        Route::post('/settings/sms-send/preview', [\App\Http\Controllers\Admin\SmsSendController::class, 'preview'])->name('settings.sms-send.preview')->middleware('can:settings.smsSend');
        Route::post('/settings/sms-send/send', [\App\Http\Controllers\Admin\SmsSendController::class, 'send'])->name('settings.sms-send.send')->middleware('can:settings.smsSend');
        Route::get('/steadfast-attempts', [\App\Http\Controllers\Admin\SteadfastAttemptController::class, 'index'])->name('steadfast-attempts.index')->middleware('can:settings.view');
        Route::get('/steadfast-attempts/{steadfastAttempt}', [\App\Http\Controllers\Admin\SteadfastAttemptController::class, 'show'])->name('steadfast-attempts.show')->middleware('can:settings.view');
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
        Route::post('/purchases/own', [\App\Http\Controllers\Sponsor\PurchaseController::class, 'storeOwn'])->name('purchases.store-own');
        Route::post('/purchases/team/{referral}', [\App\Http\Controllers\Sponsor\PurchaseController::class, 'storeTeam'])->name('purchases.store-team');

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
        
        // Gallery
        Route::get('/gallery', [\App\Http\Controllers\Sponsor\GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery', [\App\Http\Controllers\Sponsor\GalleryController::class, 'store'])->name('gallery.store');
        Route::delete('/gallery/{photo}', [\App\Http\Controllers\Sponsor\GalleryController::class, 'destroy'])->name('gallery.destroy');
        Route::get('/profile/edit', [SponsorDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [SponsorDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [SponsorDashboardController::class, 'updatePassword'])->name('profile.update-password');
    });
});
