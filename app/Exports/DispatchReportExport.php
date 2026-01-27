<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DispatchReportExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        $dispatches = DB::table('dispatches as d')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->join('products as p', 'p.id', '=', 'o.product_id')
            ->leftJoin('drivers as dr', 'dr.id', '=', 'd.driver_id')
            ->select(
                'o.order_number',
                'o.client_name',
                'p.product_name',

                // ✅ FIX IS HERE
                'o.quantity',

                'd.dispatch_date',
                'd.vehicle_number',
                DB::raw('COALESCE(dr.name, "-") as driver_name')
            )
            ->orderBy('d.dispatch_date', 'desc')
            ->get();

        $rows = $dispatches->map(fn ($d) => [
            $d->order_number,
            $d->client_name,
            $d->product_name,
            $d->quantity,
            $d->dispatch_date,
            $d->vehicle_number,
            $d->driver_name,
        ])->toArray();

        // Summary
        $rows[] = [];
        $rows[] = ['SUMMARY'];
        $rows[] = ['Total Dispatches', $dispatches->count()];
        $rows[] = ['Total Units Dispatched', $dispatches->sum('quantity')];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Client Name',
            'Product',
            'Quantity',
            'Dispatch Date',
            'Vehicle Number',
            'Driver Name',
        ];
    }
}
