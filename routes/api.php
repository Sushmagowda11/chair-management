<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\BomController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderComponentController;
use App\Http\Controllers\Api\DispatchController;
use App\Http\Controllers\Api\ReportController;
    use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::get('/panel/status', [AuthController::class, 'panelCheckStatus']);
Route::get('/panel/env', [AuthController::class, 'panelFetchDotenv']);

/*
|--------------------------------------------------------------------------
| Protected Routes (SANCTUM)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // 🔐 Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 📦 Products
  Route::apiResource('products', ProductController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);


    // 🧩 Components
   Route::apiResource('components', ComponentController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);


    // 🧾 BOM
    Route::apiResource('boms', BomController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // 🚚 Dispatch
    Route::post('dispatch', [DispatchController::class, 'store']);
    Route::get('dispatch-history', [DispatchController::class, 'history']);

    // 📊 Inventory
    Route::prefix('inventory')->group(function () {
        Route::get('current-stock', [InventoryController::class, 'currentStock']);
        Route::get('low-stock', [InventoryController::class, 'lowStock']);
        Route::get('stock-movements', [StockMovementController::class, 'index']);
        Route::post('stock-movements', [StockMovementController::class, 'store']);
    });

    // 🛒 Orders
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('orders/production-counts', [OrderController::class, 'dashboardCounts']);
    Route::get('orders/production', [OrderController::class, 'ordersInProduction']);
    Route::get('orders/ready-for-dispatch', [OrderController::class, 'readyForDispatch']);
    Route::get('orders/dispatch-history', [OrderController::class, 'dispatchHistory']);
    Route::get('production-orders', [OrderController::class, 'productionOrders']);

    Route::put('order-components/{id}', [OrderComponentController::class, 'update']);
    Route::delete('order-components/{id}', [OrderComponentController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | 📑 REPORTS
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->group(function () {

        Route::get('opening-stock', [ReportController::class, 'openingStock']);
        Route::get('stock-movements', [ReportController::class, 'stockMovements']);
        Route::get('component-consumption', [ReportController::class, 'componentConsumption']);
        Route::get('order-status', [ReportController::class, 'orderStatus']);
        Route::get('dispatch', [ReportController::class, 'dispatchReport']);

        // 📤 EXPORTS
        Route::get('opening-stock/export', [ReportController::class, 'exportOpeningStock']);
        Route::get('stock-movements/export', [ReportController::class, 'exportStockMovements']);
        Route::get('component-consumption/export', [ReportController::class, 'exportComponentConsumption']);
        Route::get('order-status/export', [ReportController::class, 'exportOrderStatus']);
        Route::get('dispatch/export', [ReportController::class, 'exportDispatchReport']);

    });

Route::get('dashboard', [DashboardController::class, 'index']);


});
