<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BomController extends Controller
{
    /**
     * GET: List all BOMs (UI-ready format)
     */
    public function index()
    {
        $boms = Bom::with(['product', 'items.component'])->get();

        $response = $boms->map(function ($bom) {
            $estimatedCost = 0;

            $items = $bom->items->map(function ($item) use (&$estimatedCost) {
                $unitPrice = $item->component->price;
                $totalCost = $unitPrice * $item->quantity;
                $estimatedCost += $totalCost;

                return [
                    'component_name' => $item->component->component_name,
                    'category'       => $item->component->category,
                    'quantity'       => $item->quantity,
                    'unit_price'     => (float) $unitPrice,
                    'total_cost'     => $totalCost,
                    'current_stock'  => $item->component->current_stock,
                ];
            });

            return [
                'bom_id'          => $bom->id,
                'product'         => $bom->product->product_name,
                'component_count' => $items->count(),
                'estimated_cost'  => $estimatedCost,
                'items'           => $items,
            ];
        });

        return response()->json($response);
    }

    /**
     * POST: Create BOM
     * One BOM per product (old BOM replaced)
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'                 => 'required|exists:products,id',
            'components'                 => 'required|array|min:1',
            'components.*.component_id'  => 'required|exists:components,id',
            'components.*.quantity'      => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            // Ensure only ONE BOM per product
            Bom::where('product_id', $request->product_id)->delete();

            $bom = Bom::create([
                'product_id' => $request->product_id,
            ]);

            foreach ($request->components as $item) {
                BomItem::create([
                    'bom_id'       => $bom->id,
                    'component_id' => $item['component_id'],
                    'quantity'     => $item['quantity'],
                ]);
            }
        });

        return response()->json([
            'message' => 'BOM created successfully',
        ], 201);
    }

    /**
     * PUT: Update BOM (edit components & quantities)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'components'                 => 'required|array|min:1',
            'components.*.component_id'  => 'required|exists:components,id',
            'components.*.quantity'      => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {

            $bom = Bom::findOrFail($id);

            // Remove old items
            $bom->items()->delete();

            // Insert updated items
            foreach ($request->components as $item) {
                BomItem::create([
                    'bom_id'       => $bom->id,
                    'component_id' => $item['component_id'],
                    'quantity'     => $item['quantity'],
                ]);
            }
        });

        return response()->json([
            'message' => 'BOM updated successfully',
        ]);
    }

    /**
     * DELETE: Remove BOM
     */
    public function destroy($id)
    {
        $bom = Bom::findOrFail($id);

        // Delete child items first
        $bom->items()->delete();

        // Delete BOM
        $bom->delete();

        return response()->json([
            'message' => 'BOM deleted successfully',
        ]);
    }
}
