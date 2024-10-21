<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Middleware\BlockDesktopAccess;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');

// Registration Confirmation
Route::get('/registration/confirmation', [RegisterController::class, 'showConfirmationPage'])->name('registration.confirmation');

// Email Verification Routes
Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('auth.verify.notice');
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('auth.verify');
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->name('auth.verify.resend');

// Password Reset Routes
Route::get('/reset-password', [PasswordResetController::class, 'showRequestForm'])->middleware('guest')->name('password.request');
Route::post('/reset-password/send-email', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

// Protected Routes
Route::middleware(['auth', BlockDesktopAccess::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');
});
