<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\EmailVerificationController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.store');
});

Route::get('/email/verify', [EmailVerificationController::class, 'showVerificationNotice'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationNotification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/machines/import', [MachineController::class, 'importForm'])->name('machines.import');
    Route::post('/machines/import', [MachineController::class, 'import'])->name('machines.import.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource("machines", MachineController::class);
    Route::get('/dashboard', [MachineController::class, 'dashboard'])->name('dashboard');
    Route::resource("users", UserController::class);
    Route::get('/account/password', [UserController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [UserController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/users/{user}/send-email', [UserController::class, 'sendEmail'])->name('users.sendEmail');
    Route::post('/users/{user}/send-reset', [UserController::class, 'sendResetLink'])->name('users.sendResetLink');
    Route::post('/users/{user}/enable', [UserController::class, 'enable'])->name('users.enable');
    Route::post('/users/{user}/disable', [UserController::class, 'disable'])->name('users.disable');
});