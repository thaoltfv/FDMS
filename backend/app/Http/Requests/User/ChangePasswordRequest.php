<?php

namespace App\Http\Requests\User;
use App\Http\Requests\FormRequest;

class ChangePasswordRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'new_password' => [
                'required',
                'min:8',
            ],
            'confirm_new_password' => [
                'required',
                'min:8',
            ],
        ];
    }
}
