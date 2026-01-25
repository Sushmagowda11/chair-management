<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /* ===============================
         | TOP SUMMARY CARDS
         =============================== */
        $totalProducts = DB::table('products')->count();
        $totalComponents = DB::table('components')->count();

        $lowStockCount = DB::table('components')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();

        $pendingOrders = DB::table('orders')
            ->where('status', 'Order Received')
            ->count();

        $inProduction = DB::table('orders')
            ->where('status', 'In Production')
            ->count();

        $readyToDispatch = DB::table('orders')
            ->where('status', 'Ready for Dispatch')
            ->count();


        /* ===============================
         | ORDER STATUS DISTRIBUTION
         =============================== */
        $orderStatusChart = DB::table('orders')
            ->select('status', DB::raw('COUNT(id) as count'))
            ->groupBy('status')
            ->get();


        /* ===============================
         | COMPONENTS BY CATEGORY
         =============================== */
        $componentsByCategory = DB::table('components')
            ->select('category', DB::raw('COUNT(id) as total'))
            ->groupBy('category')
            ->get();


        /* ===============================
         | LOW STOCK ITEMS
         =============================== */
        $lowStockItems = DB::table('components')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->select(
                'component_name',
                'component_code',
                'current_stock',
                'minimum_stock'
            )
            ->orderBy('current_stock')
            ->get();


        /* ===============================
         | RECENT DISPATCHES
         =============================== */
        $recentDispatches = DB::table('dispatches as d')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->join('products as p', 'p.id', '=', 'o.product_id')
            ->select(
                'o.order_number',
                'p.product_name',
                'd.dispatch_date'
            )
            ->groupBy(
                'd.order_id',
                'o.order_number',
                'p.product_name',
                'd.dispatch_date'
            )
            ->orderBy('d.dispatch_date', 'desc')
            ->limit(5)
            ->get();


        /* ===============================
         | FINAL RESPONSE (DATA ONLY)
         =============================== */
        return response()->json([
            'cards' => [
                'total_products'    => $totalProducts,
                'total_components'  => $totalComponents,
                'low_stock_alerts'  => $lowStockCount,
                'pending_orders'    => $pendingOrders,
                'in_production'     => $inProduction,
                'ready_to_dispatch' => $readyToDispatch,
            ],
            'order_status_distribution' => $orderStatusChart,
            'components_by_category'    => $componentsByCategory,
            'low_stock_items'           => $lowStockItems,
            'recent_dispatches'         => $recentDispatches,
        ], 200)
        // ✅ FRONTEND-ONLY STATUS (NOT IN BODY)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.dashboard_loaded'));
    }
}
