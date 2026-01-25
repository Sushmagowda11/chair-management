<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderComponent;
use App\Models\Component;
use Illuminate\Http\Request;

class OrderComponentController extends Controller
{
    /**
     * Update order component snapshot
     */
    public function update(Request $request, $id)
    {
        $item = OrderComponent::with('order')->findOrFail($id);

        // 🔒 Status guard
        if ($item->order->status !== 'In Production') {
            return response()->json(null, 403)
                ->header('X-STATUS-CODE', 403)
                ->header('X-STATUS', 'fail')
                ->header(
                    'X-STATUS-MSG',
                    config('messages.order_component_update_not_allowed')
                );
        }

        // Component change
        if ($request->filled('component_id')) {
            $component = Component::findOrFail($request->component_id);

            $item->component_id   = $component->id;
            $item->component_name = $component->component_name;
            $item->component_unit = $component->unit;
        }

        // Quantity update
        if ($request->filled('quantity_per_unit')) {
            $item->quantity_per_unit = $request->quantity_per_unit;
            $item->total_quantity =
                $item->quantity_per_unit * $item->order->quantity;
        }

        $item->save();

        return response()->json([
            'data' => $item
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header(
            'X-STATUS-MSG',
            config('messages.order_component_updated')
        );
    }

    /**
     * Delete order component snapshot
     */
    public function destroy($id)
    {
        $item = OrderComponent::with('order')->findOrFail($id);

        // 🔒 Status lock
        if ($item->order->status !== 'In Production') {
            return response()->json(null, 403)
                ->header('X-STATUS-CODE', 403)
                ->header('X-STATUS', 'fail')
                ->header(
                    'X-STATUS-MSG',
                    config('messages.order_component_delete_not_allowed')
                );
        }

        $item->delete();

        return response()->json(null, 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header(
                'X-STATUS-MSG',
                config('messages.order_component_deleted')
            );
    }
}
