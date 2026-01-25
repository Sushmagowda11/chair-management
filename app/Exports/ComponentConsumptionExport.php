<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ComponentConsumptionExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('components as c')
            ->leftJoin('stock_movements as sm', function ($join) {
                $join->on('sm.component_id', '=', 'c.id')
                     ->where('sm.movement_type', 'OUTWARD');
            })
            ->select(
                'c.component_name',
                'c.category',
                DB::raw('COALESCE(SUM(sm.quantity), 0) as total_consumed'),
                'c.current_stock',
                'c.minimum_stock'
            )
            ->groupBy(
                'c.id',
                'c.component_name',
                'c.category',
                'c.current_stock',
                'c.minimum_stock'
            )
            ->orderBy('c.component_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Component',
            'Category',
            'Total Consumed',
            'Current Stock',
            'Min Stock'
        ];
    }
}
