<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'sku'      => $this->sku,
            'price'    => $this->price,
            'stock'    => $this->stock,
            'category' => $this->category->name ?? null,
        ];
    }
}

