<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BorrowingApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Bonus Fitur - REST API)
|--------------------------------------------------------------------------
|
| Autentikasi menggunakan Laravel Sanctum (Bearer Token).
| Lihat API_DOCUMENTATION.md untuk detail lengkap setiap endpoint.
|
*/

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    Route::apiResource('products', ProductApiController::class);

    Route::get('/borrowings', [BorrowingApiController::class, 'index']);
    Route::post('/borrowings', [BorrowingApiController::class, 'store']);
    Route::get('/borrowings/{borrowing}', [BorrowingApiController::class, 'show']);
    Route::patch('/borrowings/{borrowing}/return', [BorrowingApiController::class, 'returnItem']);

    Route::get('/dashboard/summary', [ProductApiController::class, 'dashboardSummary']);
});
