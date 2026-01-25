<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderStatusExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        $orders = DB::table('orders as o')
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
            )
            ->get();

        $rows = $orders->map(fn ($o) => [
            $o->order_number,
            $o->client_name,
            $o->product_name,
            $o->quantity,
            $o->order_date,
            $o->expected_delivery,
            $o->total_amount,
            $o->status,
        ])->toArray();

        // 🔹 Summary rows
        $rows[] = [];
        $rows[] = ['SUMMARY'];
        $rows[] = ['Total Orders', $orders->count()];
        $rows[] = ['Total Quantity', $orders->sum('quantity')];
        $rows[] = ['Total Value', $orders->sum('total_amount')];
        $rows[] = ['Completed Orders', $orders->where('status','Completed')->count()];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Client Name',
            'Product',
            'Quantity',
            'Order Date',
            'Expected Delivery',
            'Total Amount',
            'Status',
        ];
    }
}
