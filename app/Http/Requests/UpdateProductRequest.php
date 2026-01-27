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
<<<<<<< HEAD
        'product_code' => 'sometimes|required|string',
        'product_name' => 'sometimes|required|string',
        'category'     => 'sometimes|required|string',
        'price'        => 'sometimes|required|numeric',
        'description'  => 'nullable|string',
        'status'       => 'sometimes|required|integer',
    ];
=======
            'product_code' => 'required|string|unique:products,product_code,' . $productId,
            'product_name' => 'required|string|max:255',
            'category'     => 'required|string',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
            'status'       => 'required|boolean',
        ];
>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
    }
}
