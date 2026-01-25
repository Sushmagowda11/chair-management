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
    try {
        $boms = Bom::with(['product', 'items.component'])->get();

        if ($boms->isEmpty()) {
            return response()->json([], 404)
                ->header('X-STATUS-CODE', 404)
                ->header('X-STATUS', 'fail')
                ->header('X-STATUS-MSG', config('messages.data_not_found'));
        }

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

        return response()->json([
            'data' => $response,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.bom_list_fetched'));

    } catch (\Exception $e) {
        return response()->json([], 500)
            ->header('X-STATUS-CODE', 500)
            ->header('X-STATUS', 'fail')
            ->header('X-STATUS-MSG', config('messages.something_went_wrong'));
    }
}


    /**
     * POST: Create BOM
     */
    public function store(Request $request)
{
    $request->validate([
        'product_id'                => 'required|exists:products,id',
        'components'                => 'required|array|min:1',
        'components.*.component_id' => 'required|exists:components,id',
        'components.*.quantity'     => 'required|integer|min:1',
    ]);

    try {
        DB::transaction(function () use ($request) {
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

        return response()->json([], 201)
            ->header('X-STATUS-CODE', 201)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.bom_created'));

    } catch (\Exception $e) {
        return response()->json([], 500)
            ->header('X-STATUS-CODE', 500)
            ->header('X-STATUS', 'fail')
            ->header('X-STATUS-MSG', config('messages.something_went_wrong'));
    }
}


    /**
     * PUT: Update BOM
     */
   public function update(Request $request, $id)
{
    $request->validate([
        'components'                => 'required|array|min:1',
        'components.*.component_id' => 'required|exists:components,id',
        'components.*.quantity'     => 'required|integer|min:1',
    ]);

    try {
        DB::transaction(function () use ($request, $id) {
            $bom = Bom::findOrFail($id);
            $bom->items()->delete();

            foreach ($request->components as $item) {
                BomItem::create([
                    'bom_id'       => $bom->id,
                    'component_id' => $item['component_id'],
                    'quantity'     => $item['quantity'],
                ]);
            }
        });

        return response()->json([], 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.bom_updated'));

    } catch (\Exception $e) {
        return response()->json([], 500)
            ->header('X-STATUS-CODE', 500)
            ->header('X-STATUS', 'fail')
            ->header('X-STATUS-MSG', config('messages.something_went_wrong'));
    }
}


    /**
     * DELETE: Remove BOM
     */
  public function destroy($id)
{
    try {
        $bom = Bom::findOrFail($id);
        $bom->items()->delete();
        $bom->delete();

        return response()->json([], 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.bom_deleted'));

    } catch (\Exception $e) {
        return response()->json([], 500)
            ->header('X-STATUS-CODE', 500)
            ->header('X-STATUS', 'fail')
            ->header('X-STATUS-MSG', config('messages.something_went_wrong'));
    }
}

}
