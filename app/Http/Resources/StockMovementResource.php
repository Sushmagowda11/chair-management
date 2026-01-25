<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->created_at->format('Y-m-d'),
            'component' => $this->component->component_name,
            'type' => ucfirst(strtolower($this->movement_type)),
            'quantity' => $this->movement_type === 'OUTWARD'
                ? -$this->quantity
                : +$this->quantity,
            'reference' => $this->reference,
        ];
    }
}
