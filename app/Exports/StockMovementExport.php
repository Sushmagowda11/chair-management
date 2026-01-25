<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockMovementExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('stock_movements as sm')
            ->join('components as c', 'c.id', '=', 'sm.component_id')
            ->select(
                DB::raw('DATE(sm.created_at) as date'),
                'c.component_name',
                'sm.movement_type',
                DB::raw("
                    CASE
                        WHEN sm.movement_type = 'OUTWARD'
                            THEN CONCAT('-', sm.quantity)
                        ELSE CONCAT('+', sm.quantity)
                    END as quantity
                "),
                'sm.reference'
            )
            ->orderBy('sm.created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Component',
            'Type',
            'Quantity',
            'Reference'
        ];
    }
}
