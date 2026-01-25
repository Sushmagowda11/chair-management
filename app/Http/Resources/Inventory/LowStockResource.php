<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'component_name' => $this->component_name,
            'code' => $this->component_code,
            'current_stock' => $this->current_stock,
            'min_stock' => $this->minimum_stock,
            'required' => $this->minimum_stock - $this->current_stock,
        ];
    }
}
