<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * @var mixed
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:250',
            ],
            'permissions' => [
                'required',
                'array',
            ]
        ];
    }
}
