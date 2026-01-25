<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ProductionDispatchController extends Controller
{
    /**
     * 📊 Dashboard counts
     */
    public function dashboardCounts()
    {
        return response()->json([
            'data' => [
                'in_production'      => Order::where('status', 'In Production')->count(),
                'ready_for_dispatch' => Order::where('status', 'Ready for Dispatch')->count(),
                'dispatched'         => Order::where('status', 'Dispatched')->count(),
            ],
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header(
            'X-STATUS-MSG',
            config('messages.production_dashboard_counts')
        );
    }

    /**
     * 🏭 Orders in Production
     */
    public function ordersInProduction()
    {
        $orders = Order::with('product')
            ->where('status', 'In Production')
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'data' => $orders,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header(
            'X-STATUS-MSG',
            config('messages.orders_in_production')
        );
    }

    /**
     * 🚚 Ready for Dispatch
     */
    public function readyForDispatch()
    {
        $orders = Order::with('product')
            ->where('status', 'Ready for Dispatch')
            ->orderBy('expected_delivery')
            ->get();

        return response()->json([
            'data' => $orders,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header(
            'X-STATUS-MSG',
            config('messages.orders_ready_dispatch')
        );
    }

    /**
     * 📜 Dispatch History
     */
    public function dispatchHistory()
    {
        $orders = Order::with('product')
            ->where('status', 'Dispatched')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'data' => $orders,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header(
            'X-STATUS-MSG',
            config('messages.orders_dispatch_history')
        );
    }

    /**
     * 🔄 Move → In Production
     */
    public function startProduction($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'In Production']);

        return response()->json(null, 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header(
                'X-STATUS-MSG',
                config('messages.order_moved_production')
            );
    }

    /**
     * 🔄 Move → Ready for Dispatch
     */
    public function markReadyForDispatch($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'Ready for Dispatch']);

        return response()->json(null, 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header(
                'X-STATUS-MSG',
                config('messages.order_ready_for_dispatch')
            );
    }

    /**
     * 🔄 Move → Dispatched
     */
    public function markDispatched($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'Dispatched']);

        return response()->json(null, 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header(
                'X-STATUS-MSG',
                config('messages.order_dispatched')
            );
    }
}
