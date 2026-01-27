<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'components' => 'required|array|min:1',

            'components.*.component_id' => [
                'required',
                'integer',
                'exists:components,id'
            ],

            'components.*.quantity' => [
                'required',
                'numeric',
                'min:1'
            ],
        ];
    }
}
