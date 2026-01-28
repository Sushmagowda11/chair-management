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
            'product_code' => 'sometimes|required|string|unique:products,product_code,' . $productId,
            'product_name' => 'sometimes|required|string|max:255',
            'category'     => 'sometimes|required|string',
            'price'        => 'sometimes|required|numeric|min:0',
            'description'  => 'nullable|string',
            'status'       => 'sometimes|required|boolean',
        ];
    }
}
