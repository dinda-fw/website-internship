<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index')->middleware('role:admin,staff,manager');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create')->middleware('role:admin,staff');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store')->middleware('role:admin,staff');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show')->middleware('role:admin,staff,manager');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit')->middleware('role:admin,staff');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('role:admin,staff');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('role:admin,staff');

    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    Route::get('/borrowings', [BorrowingController::class, 'index'])->name('borrowings.index')->middleware('role:admin,staff,manager');
    Route::get('/borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create')->middleware('role:admin,staff');
    Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store')->middleware('role:admin,staff');
    Route::get('/borrowings/{borrowing}', [BorrowingController::class, 'show'])->name('borrowings.show')->middleware('role:admin,staff,manager');
    Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnItem'])->name('borrowings.return')->middleware('role:admin,staff');

    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/export/products/pdf', [ExportController::class, 'productsPdf'])->name('export.products.pdf');
        Route::get('/export/products/excel', [ExportController::class, 'productsExcel'])->name('export.products.excel');
        Route::get('/export/borrowings/pdf', [ExportController::class, 'borrowingsPdf'])->name('export.borrowings.pdf');
    });
});
