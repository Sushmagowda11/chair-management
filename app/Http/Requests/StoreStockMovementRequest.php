<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'component_id' => 'required|exists:components,id',
            'movement_type' => 'required|in:INWARD,OUTWARD,OPENING,ADJUSTMENT',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
        ];
    }
}
