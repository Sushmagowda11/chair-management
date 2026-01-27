<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

// Exports
use App\Exports\OpeningStockExport;
use App\Exports\StockMovementExport;
use App\Exports\ComponentConsumptionExport;
use App\Exports\OrderStatusExport;
use App\Exports\DispatchReportExport;

class ReportController extends Controller
{
    /* =====================================================
     | COMMON DATE FILTER
     ===================================================== */
    private function applyDateFilter($query, $column)
    {
        $from = request('from_date');
        $to   = request('to_date');

        if ($from && $to) {
            $query->whereDate($column, '>=', $from)
                  ->whereDate($column, '<=', $to);
        }

        return $query;
    }

    /* =====================================================
     | 1. OPENING STOCK
     ===================================================== */
    public function openingStock()
    {
        $query = DB::table('components as c')
            ->leftJoin('stock_movements as sm', 'sm.component_id', '=', 'c.id')
            ->select(
                'c.component_code',
                'c.component_name',
                'c.category',
                'c.unit',
                'c.minimum_stock',
                'c.price as unit_price',
                DB::raw("
                    COALESCE(SUM(
                        CASE
                            WHEN sm.movement_type IN ('OPENING','INWARD','ADJUSTMENT')
                                THEN sm.quantity
                            WHEN sm.movement_type = 'OUTWARD'
                                THEN -sm.quantity
                            ELSE 0
                        END
                    ),0) as current_stock
                ")
            );

        $this->applyDateFilter($query, 'sm.created_at');

        $data = $query
            ->groupBy(
                'c.id',
                'c.component_code',
                'c.component_name',
                'c.category',
                'c.unit',
                'c.minimum_stock',
                'c.price'
            )
            ->orderBy('c.component_name')
            ->get()
            ->map(function ($row) {
                $row->stock_value = $row->current_stock * $row->unit_price;
                $row->status = $row->current_stock <= $row->minimum_stock
                    ? 'Low Stock'
                    : 'Adequate';
                return $row;
            });

        $summary = [
            'total_components'  => $data->count(),
            'low_stock_items'   => $data->where('status', 'Low Stock')->count(),
            'total_stock_value' => $data->sum('stock_value'),
        ];

        return response()
            ->json([
                'data'    => $data,
                'summary' => $summary
            ])
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.report_opening_stock_loaded'));
    }

    /* =====================================================
     | 2. STOCK MOVEMENTS
     ===================================================== */
    public function stockMovements()
    {
        $query = DB::table('stock_movements as sm')
            ->join('components as c', 'c.id', '=', 'sm.component_id')
            ->select(
                DB::raw('DATE(sm.created_at) as date'),
                'c.component_name as component',
                'sm.movement_type as type',
                'sm.quantity',
                DB::raw("COALESCE(sm.reference, sm.movement_type) as reference")
            );

        $this->applyDateFilter($query, 'sm.created_at');

        $data = $query
            ->orderBy('sm.created_at', 'desc')
            ->get()
            ->map(function ($row) {
                $row->display_quantity =
                    $row->type === 'OUTWARD'
                        ? '-' . $row->quantity
                        : '+' . $row->quantity;
                return $row;
            });

        return response()
            ->json(['data' => $data])
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.report_stock_movements_loaded'));
    }

    /* =====================================================
     | 3. COMPONENT CONSUMPTION
     ===================================================== */
    public function componentConsumption()
    {
        $query = DB::table('components as c')
            ->leftJoin('stock_movements as sm', function ($join) {
                $join->on('sm.component_id', '=', 'c.id')
                     ->where('sm.movement_type', 'OUTWARD');
            })
            ->select(
                'c.component_name as component',
                'c.category',
                DB::raw('COALESCE(SUM(sm.quantity),0) as total_consumed'),
                'c.current_stock',
                'c.minimum_stock'
            );

        $this->applyDateFilter($query, 'sm.created_at');

        $data = $query
            ->groupBy(
                'c.id',
                'c.component_name',
                'c.category',
                'c.current_stock',
                'c.minimum_stock'
            )
            ->orderBy('c.component_name')
            ->get();

        return response()
            ->json(['data' => $data])
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.report_component_consumption_loaded'));
    }

    /* =====================================================
     | 4. ORDER STATUS
     ===================================================== */
    public function orderStatus()
    {
        $query = DB::table('orders as o')
            ->join('products as p', 'p.id', '=', 'o.product_id')
            ->select(
                'o.order_number',
                'o.client_name',
                'p.product_name',
                'o.quantity',
                'o.order_date',
                'o.expected_delivery',
                'o.total_amount',
                'o.status'
            );

        $this->applyDateFilter($query, 'o.order_date');

        $orders = $query->orderBy('o.order_date', 'desc')->get();

        $summary = [
            'total_orders'   => $orders->count(),
            'total_quantity' => $orders->sum('quantity'),
            'total_value'    => $orders->sum('total_amount'),
            'completed'      => $orders->where('status', 'Completed')->count(),
        ];

        return response()
            ->json([
                'data'    => $orders,
                'summary' => $summary
            ])
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.report_order_status_loaded'));
    }

    /* =====================================================
     | 5. DISPATCH REPORT
     ===================================================== */
    public function dispatchReport()
    {
        $query = DB::table('dispatches as d')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->join('products as p', 'p.id', '=', 'o.product_id')
            ->leftJoin('drivers as dr', 'dr.id', '=', 'd.driver_id')
            ->select(
                'o.order_number',
                'o.client_name',
                'p.product_name',
                'o.quantity',
                'd.dispatch_date',
                'd.vehicle_number',
                DB::raw('COALESCE(dr.name, "-") as driver_name')
            );

        $this->applyDateFilter($query, 'd.dispatch_date');

        $dispatches = $query->orderBy('d.dispatch_date', 'desc')->get();

        $summary = [
            'total_dispatches' => $dispatches->count(),
            'total_units'      => $dispatches->sum('quantity'),
        ];

        return response()
            ->json([
                'data'    => $dispatches,
                'summary' => $summary
            ])
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.report_dispatch_loaded'));
    }

    /* =====================================================
     | EXPORTS
     ===================================================== */
    public function exportOpeningStock()
    {
        return Excel::download(
            new OpeningStockExport(request('from_date'), request('to_date')),
            'opening-stock-report.xlsx'
        );
    }

    public function exportStockMovements()
    {
        return Excel::download(
            new StockMovementExport(request('from_date'), request('to_date')),
            'stock-movement-report.xlsx'
        );
    }

    public function exportComponentConsumption()
    {
        return Excel::download(
            new ComponentConsumptionExport(request('from_date'), request('to_date')),
            'component-consumption-report.xlsx'
        );
    }

    public function exportOrderStatus()
    {
        return Excel::download(
            new OrderStatusExport(request('from_date'), request('to_date')),
            'order-status-report.xlsx'
        );
    }

    public function exportDispatchReport()
    {
        return Excel::download(
            new DispatchReportExport(request('from_date'), request('to_date')),
            'dispatch-report.xlsx'
        );
    }
}
