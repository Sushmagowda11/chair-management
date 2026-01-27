<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\CurrentStockRequest;
use App\Http\Resources\Inventory\CurrentStockResource;
use App\Http\Resources\Inventory\LowStockResource;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /* ===============================
     | CURRENT STOCK TABLE
     =============================== */
    public function currentStock(CurrentStockRequest $request)
    {
        $components = $this->inventoryService->getCurrentStock();

        if ($components->isEmpty()) {
            return response()->json([], 404)
                ->header('X-STATUS-CODE', 404)
                ->header('X-STATUS', 'fail')
                ->header('X-STATUS-MSG', config('messages.data_not_found'));
        }

        return response()->json([
            'data' => CurrentStockResource::collection($components),
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.inventory_current_stock_loaded'));
    }

    /* ===============================
     | LOW STOCK CARDS (TOP SECTION)
     =============================== */
    public function lowStock()
    {
        $components = $this->inventoryService->getLowStock();

        if ($components->isEmpty()) {
            return response()->json([], 404)
                ->header('X-STATUS-CODE', 404)
                ->header('X-STATUS', 'fail')
                ->header('X-STATUS-MSG', config('messages.data_not_found'));
        }

        return response()->json([
            'data' => LowStockResource::collection($components),
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.inventory_low_stock_loaded'));
    }
}
