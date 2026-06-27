<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockAdjustmentController;
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

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/account/password', [UserController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [UserController::class, 'updatePassword'])->name('account.password.update');

    Route::middleware('role:viewer,editor,admin,superadmin')->group(function () {
        Route::get('/dashboard', [MachineController::class, 'dashboard'])->name('dashboard');

        Route::get('/machines/export', [MachineController::class, 'export'])->name('machines.export');
        Route::resource('machines', MachineController::class)->only(['index', 'show'])->whereNumber('machine');

        Route::get('/units/export', [UnitController::class, 'export'])->name('units.export');
        Route::resource('units', UnitController::class)->only(['index']);

        Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
        Route::resource('categories', CategoryController::class)->only(['index']);

        Route::get('/parts/search', [PartController::class, 'search'])->name('parts.search');
        Route::get('/parts/export', [PartController::class, 'export'])->name('parts.export');
        Route::resource('parts', PartController::class)->only(['index', 'show'])->whereNumber('part');

        Route::get('/purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
        Route::resource('purchases', PurchaseController::class)->only(['index']);

        Route::get('/issues/export', [IssueController::class, 'export'])->name('issues.export');
        Route::resource('issues', IssueController::class)->only(['index']);

        Route::get('/stock-adjustments/export', [StockAdjustmentController::class, 'export'])->name('stock-adjustments.export');
        Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index']);

        Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::get('/stocks/daily/export', [StockController::class, 'dailyExport'])->name('stocks.daily.export');
    });

    Route::middleware('role:editor,admin,superadmin')->group(function () {
        Route::get('/machines/import', [MachineController::class, 'importForm'])->name('machines.import');
        Route::post('/machines/import', [MachineController::class, 'import'])->name('machines.import.store');
        Route::resource('machines', MachineController::class)->only(['create', 'store', 'edit', 'update']);
        Route::get('/machines/{machine}/parts', [MachineController::class, 'editParts'])->name('machines.parts.edit');
        Route::post('/machines/{machine}/parts', [MachineController::class, 'updateParts'])->name('machines.parts.update');
        Route::get('/machines/{machine}/copy-list', [MachineController::class, 'listForCopy'])->name('machines.copy.list');
        Route::post('/machines/{machine}/copy-to', [MachineController::class, 'copyTo'])->name('machines.copy.to');

        Route::resource('units', UnitController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('categories', CategoryController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('parts', PartController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('purchases', PurchaseController::class)->only(['create', 'store']);
        Route::resource('issues', IssueController::class)->only(['create', 'store']);
        Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['create', 'store']);
    });

    Route::middleware('role:admin,superadmin')->group(function () {
        Route::resource('machines', MachineController::class)->only(['destroy']);
        Route::resource('units', UnitController::class)->only(['destroy']);
        Route::resource('categories', CategoryController::class)->only(['destroy']);
        Route::resource('parts', PartController::class)->only(['destroy']);
    });

    Route::middleware('role:superadmin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/send-email', [UserController::class, 'sendEmail'])->name('users.sendEmail');
        Route::post('/users/{user}/send-reset', [UserController::class, 'sendResetLink'])->name('users.sendResetLink');
        Route::post('/users/{user}/enable', [UserController::class, 'enable'])->name('users.enable');
        Route::post('/users/{user}/disable', [UserController::class, 'disable'])->name('users.disable');
    });
});
