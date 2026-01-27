<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class CurrentStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // later role-based access
    }

    public function rules(): array
    {
        return [
            // No filters for now
        ];
    }
}
