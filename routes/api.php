<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\BomController;

// 🔓 LOGIN (NO AUTH)
Route::post('/login', [AuthController::class, 'login']);

// 🔐 AUTHENTICATED ROUTES
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Components
    Route::get('/components', [ComponentController::class, 'index']);
    Route::post('/components', [ComponentController::class, 'store']);
    Route::put('/components/{component}', [ComponentController::class, 'update']);
    Route::delete('/components/{component}', [ComponentController::class, 'destroy']);

    // ✅ BOM (ALL CRUD)
    Route::prefix('boms')->group(function () {
        Route::get('/', [BomController::class, 'index']);
        Route::post('/', [BomController::class, 'store']);
        Route::put('/{bom}', [BomController::class, 'update']);   // ✅ FIXED
        Route::delete('/{bom}', [BomController::class, 'destroy']);
    });
});
