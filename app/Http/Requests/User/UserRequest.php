<?php

namespace App\Http\Requests\User;
use App\Http\Requests\FormRequest;
use App\Models\Role;

class UserRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:250',
            ],
            'role' => [
                'required'
            ],
        ];
    }
}
