<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->component_code,
            'component_name' => $this->component_name,
            'category' => $this->category,
            'current_stock' => $this->current_stock,
            'min_stock' => $this->minimum_stock,
            'status' => $this->current_stock < $this->minimum_stock
                ? 'Low Stock'
                : 'Adequate',
            'unit' => $this->unit,
            'value' => $this->current_stock * $this->price,
        ];
    }
}
