<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;

class StockMovementController extends Controller
{
    /**
     * 📦 Stock movement table
     */
    public function index()
    {
        $movements = StockMovement::with('component')
            ->latest()
            ->get();

        return response()->json([
            'data' => StockMovementResource::collection($movements),
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.stock_movement_list_fetched'));
    }

    /**
     * ➕ Store stock movement (log only)
     */
    public function store(StoreStockMovementRequest $request)
    {
        $movement = StockMovement::create($request->validated());

        return response()->json([
            'data' => new StockMovementResource(
                $movement->load('component')
            ),
        ], 201)
        ->header('X-STATUS-CODE', 201)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.stock_movement_created'));
    }
}
