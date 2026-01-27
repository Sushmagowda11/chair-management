<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
<<<<<<< HEAD
            'email' => ['required', 'email'],
            'password' => ['required'],
=======
            'email' => 'required|email',
            'password' => 'required|string',
>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
        ];
    }
}
