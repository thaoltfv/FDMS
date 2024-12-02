<?php

namespace App\Http\Requests\Auth;
use App\Http\Requests\FormRequest;

class LoginRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:250',
            ],
            'token' => [
                'required',
            ],
        ];
    }
}
