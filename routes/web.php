<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ClosetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Closet Routes
Route::middleware('auth')->group(function () {
    Route::get('/garderob', [ClosetController::class, 'index'])->name('closet.index');
    Route::get('/garderob/lagg-till', [ClosetController::class, 'create'])->name('closet.create');
    Route::post('/garderob', [ClosetController::class, 'store'])->name('closet.store');
    Route::delete('/garderob/{clothingArticle}', [ClosetController::class, 'destroy'])->name('closet.destroy');
});

// Generate Routes
Route::middleware('auth')->group(function () {
    Route::get('/omklädningsrum', [GenerateController::class, 'index'])->name('generate.index');
    Route::get('/omklädningsrum/fits', [GenerateController::class, 'recent'])->name('generate.recent');
    Route::post('/omklädningsrum/dress', [GenerateController::class, 'dressMe'])->name('generate.dressMe');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/spegel', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/spegel/bild', [ProfileController::class, 'updateProfilePicture'])->name('profile.updatePicture');
});

// Settings Routes
Route::middleware('auth')->group(function () {
    Route::get('/spegel/inställningar', [SettingsController::class, 'index'])->name('profile.settings');
});

// Login Routes
Route::get('/logga-in', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/logga-in', [AuthController::class, 'login'])->middleware('guest');

// Logout Route
Route::post('/logga-ut', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Registration Routes
Route::get('/registrera', [AuthController::class, 'showRegistrationForm'])->middleware('guest')->name('register.index');
Route::post('/registrera', [AuthController::class, 'register'])->middleware('guest')->name('register');
Route::get('/registrera/bekräftelse', [AuthController::class, 'showConfirmationMessage'])->name('registration.confirmation');

// Email Verification Routes
Route::get('/epost/verifiera', [AuthController::class, 'showVerificationNotice'])->name('auth.verify.notice');
Route::get('/epost/verifiera/{token}', [AuthController::class, 'verifyEmail'])->name('auth.verify');

// Password Reset Routes
Route::get('/lösenord/återställ', [PasswordResetController::class, 'showRequestForm'])->middleware('guest')->name('password.request');
Route::post('/lösenord/återställ', [PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');
Route::post('/lösenord/epost', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/lösenord/återställ/{token}', [PasswordResetController::class, 'showResetForm'])->middleware('guest')->name('password.reset');

// Delete account
Route::delete('/account', [SettingsController::class, 'destroy'])->middleware('auth')->name('account.destroy');

// Error Routes
Route::get('/ogiltig-token', function () {
    return view('auth.errors.invalid_token');
})->name('errors.invalid_token');
Route::get('/skrivbord', function () {
    return view('auth.errors.desktop');
})->name('errors.desktop');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('closet.index');
    })->name('home');
});
