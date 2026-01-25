<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id');

        return [
        'product_code' => 'sometimes|required|string',
        'product_name' => 'sometimes|required|string',
        'category'     => 'sometimes|required|string',
        'price'        => 'sometimes|required|numeric',
        'description'  => 'nullable|string',
        'status'       => 'sometimes|required|integer',
    ];
    }
}
