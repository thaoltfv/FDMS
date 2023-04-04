<?php

namespace App\Http\Requests\Auth;
use App\Http\Requests\FormRequest;

class RefreshTokenRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'refreshToken' => [
                'required',
            ],
        ];
    }
}
