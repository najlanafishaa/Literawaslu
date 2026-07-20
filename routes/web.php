<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\MemberAdminController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookReviewController;

// 1. Root Route
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// 2. Authentication Routes (Guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Forgot Password routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'submitRequest'])->name('password.email');
    Route::get('/forgot-password/security-question', [ForgotPasswordController::class, 'showSecurityQuestionForm'])->name('password.security');
    Route::post('/forgot-password/security-question', [ForgotPasswordController::class, 'verifySecurityQuestion'])->name('password.security_verify');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// 3. Protected Routes (Authenticated users only)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete_photo');

    // ============================================
    // MEMBER SPECIFIC ROUTES
    // ============================================
    Route::middleware('role:member')->group(function () {
        Route::get('/catalog', [MemberController::class, 'catalog'])->name('catalog');
        Route::get('/member/card', [MemberController::class, 'card'])->name('member.card');
        Route::get('/member/history', [MemberController::class, 'history'])->name('member.history');
        Route::get('/member/rewards', [MemberController::class, 'rewards'])->name('member.rewards');
        Route::post('/member/redeem', [MemberController::class, 'redeem'])->name('member.redeem');
        Route::post('/member/request-borrow', [MemberController::class, 'requestBorrow'])->name('member.request_borrow');
        // Book reviews
        Route::post('/catalog/{book}/review', [BookReviewController::class, 'store'])->name('book.review.store');
        Route::delete('/reviews/{review}', [BookReviewController::class, 'destroy'])->name('book.review.destroy');
        // Read online (Google Drive preview)
        Route::get('/catalog/{book}/read', [BookController::class, 'read'])->name('book.read');
    });

    // ============================================
    // PETUGAS & SUPER ADMIN SHARED ROUTES
    // ============================================
    Route::middleware('role:petugas,super_admin')->group(function () {
        Route::get('/borrows', [BorrowController::class, 'index'])->name('borrows.index');
        Route::post('/borrows/checkout', [BorrowController::class, 'checkout'])->name('borrows.checkout');
        Route::post('/borrows/checkin', [BorrowController::class, 'checkin'])->name('borrows.checkin');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/members', [MemberAdminController::class, 'index'])->name('members.index');
        
        // Verifications
        Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
        Route::post('/verifications/member/{member}/approve', [VerificationController::class, 'approveMember'])->name('verifications.member.approve');
        Route::post('/verifications/member/{member}/reject', [VerificationController::class, 'rejectMember'])->name('verifications.member.reject');
        Route::post('/verifications/borrow/{borrow}/approve', [VerificationController::class, 'approveBorrow'])->name('verifications.borrow.approve');
        Route::post('/verifications/borrow/{borrow}/reject', [VerificationController::class, 'rejectBorrow'])->name('verifications.borrow.reject');
        Route::post('/verifications/reset-request/{resetRequest}/approve', [VerificationController::class, 'approveResetRequest'])->name('verifications.reset.approve');
        Route::post('/verifications/reset-request/{resetRequest}/reject', [VerificationController::class, 'rejectResetRequest'])->name('verifications.reset.reject');

        // Books CRUD
        Route::resource('/admin/books', BookController::class)->names([
            'index'   => 'books.index',
            'create'  => 'books.create',
            'store'   => 'books.store',
            'edit'    => 'books.edit',
            'update'  => 'books.update',
            'destroy' => 'books.destroy',
        ])->except(['show']);

        // Pay fine
        Route::post('/borrows/{borrow}/pay-fine', [BorrowController::class, 'payFine'])->name('borrows.pay_fine');

        // Category CRUD
        Route::get('/admin/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/admin/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // ============================================
    // SUPER ADMIN ONLY ROUTES
    // ============================================
    Route::middleware('role:super_admin')->group(function () {
    
        // Account Management
        Route::get('/admin/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::post('/admin/accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::post('/admin/accounts/{user}/demote', [AccountController::class, 'demote'])->name('accounts.demote');

        // Member Adjustment
        Route::get('/admin/members/create', [MemberAdminController::class, 'create'])->name('members.create');
        Route::post('/admin/members', [MemberAdminController::class, 'store'])->name('members.store');
        Route::get('/admin/members/{member}/edit', [MemberAdminController::class, 'edit'])->name('members.edit');
        Route::put('/admin/members/{member}', [MemberAdminController::class, 'update'])->name('members.update');
        Route::delete('/admin/members/{member}', [MemberAdminController::class, 'destroy'])->name('members.destroy');

        // Officers CRUD
        Route::resource('/admin/officers', OfficerController::class)->names([
            'index' => 'officers.index',
            'create' => 'officers.create',
            'store' => 'officers.store',
            'edit' => 'officers.edit',
            'update' => 'officers.update',
            'destroy' => 'officers.destroy',
        ])->except(['show']);

        // Transaction logs
        Route::get('/admin/borrows/history', [BorrowController::class, 'history'])->name('borrows.history');

        // Settings & Google Sheets Sync
        Route::get('/admin/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/admin/settings/sync-sheets', [SettingController::class, 'syncSheets'])->name('settings.sync_sheets');
    });
});
