<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class DispatchController extends Controller
{
    /**
     * 🚚 Store dispatch details
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'driver_id'      => 'required|exists:drivers,id',
            'vehicle_number' => 'required|string|max:50',
            'dispatch_date'  => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        // ❌ Already dispatched
        if ($order->status === 'Dispatched') {
            return response()->json(null, 409)
                ->header('X-STATUS-CODE', 409)
                ->header('X-STATUS', 'fail')
                ->header(
                    'X-STATUS-MSG',
                    config('messages.order_already_dispatched')
                );
        }

        // ❌ Not ready for dispatch
        if ($order->status !== 'Ready for Dispatch') {
            return response()->json(null, 409)
                ->header('X-STATUS-CODE', 409)
                ->header('X-STATUS', 'fail')
                ->header(
                    'X-STATUS-MSG',
                    config('messages.order_not_ready_for_dispatch')
                );
        }

        DB::beginTransaction();

        try {
            DB::table('dispatches')->insert([
                'order_id'       => $request->order_id,
                'driver_id'      => $request->driver_id,
                'vehicle_number' => $request->vehicle_number,
                'dispatch_date'  => $request->dispatch_date,
                'notes'          => $request->notes,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $order->update([
                'status' => 'Dispatched'
            ]);

            DB::commit();

            return response()->json(null, 201)
                ->header('X-STATUS-CODE', 201)
                ->header('X-STATUS', 'ok')
                ->header(
                    'X-STATUS-MSG',
                    config('messages.order_dispatched_success')
                );

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(null, 500)
                ->header('X-STATUS-CODE', 500)
                ->header('X-STATUS', 'fail')
                ->header(
                    'X-STATUS-MSG',
                    config('messages.dispatch_failed')
                );
        }
    }

    /**
     * 📜 Dispatch history
     */
    public function history()
    {
        $data = DB::table('dispatches as d')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->join('products as p', 'p.id', '=', 'o.product_id')
            ->join('drivers as dr', 'dr.id', '=', 'd.driver_id')
            ->select(
                'o.order_number',
                'o.client_name',
                'p.product_name',
                'o.quantity',
                'd.dispatch_date',
                'd.vehicle_number',
                'dr.name as driver_name',
                'd.notes'
            )
            ->orderBy('d.dispatch_date', 'desc')
            ->get();

        return response()->json([
            'data' => $data
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header(
            'X-STATUS-MSG',
            config('messages.dispatch_history_loaded')
        );
    }
}
