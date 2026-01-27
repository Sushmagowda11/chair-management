<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OpeningStockExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('components as c')
            ->leftJoin('stock_movements as sm', 'sm.component_id', '=', 'c.id')
            ->select(
                'c.component_code',
                'c.component_name',
                'c.category',
                DB::raw("
                    COALESCE(SUM(
                        CASE
                            WHEN sm.movement_type IN ('OPENING','INWARD','ADJUSTMENT')
                                THEN sm.quantity
                            WHEN sm.movement_type = 'OUTWARD'
                                THEN -sm.quantity
                        END
                    ),0) as current_stock
                "),
                'c.minimum_stock',
                'c.price as unit_price',
                DB::raw("
                    COALESCE(SUM(
                        CASE
                            WHEN sm.movement_type IN ('OPENING','INWARD','ADJUSTMENT')
                                THEN sm.quantity
                            WHEN sm.movement_type = 'OUTWARD'
                                THEN -sm.quantity
                        END
                    ),0) * c.price as stock_value
                "),
                DB::raw("
                    CASE
                        WHEN COALESCE(SUM(
                            CASE
                                WHEN sm.movement_type IN ('OPENING','INWARD','ADJUSTMENT')
                                    THEN sm.quantity
                                WHEN sm.movement_type = 'OUTWARD'
                                    THEN -sm.quantity
                            END
                        ),0) <= c.minimum_stock
                        THEN 'Low Stock'
                        ELSE 'Adequate'
                    END as status
                ")
            )
            ->groupBy(
                'c.id',
                'c.component_code',
                'c.component_name',
                'c.category',
                'c.minimum_stock',
                'c.price'
            )
            ->orderBy('c.component_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Component Code',
            'Component Name',
            'Category',
            'Current Stock',
            'Min Stock',
            'Unit Price',
            'Stock Value',
            'Status'
        ];
    }
}
