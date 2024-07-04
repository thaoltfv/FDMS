<?php

namespace App\Http\Requests\CustomerGroup;

use App\Http\Requests\FormRequest;

class CustomerGroupSecondRequest extends FormRequest
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
            'first_id' => [
                'required',
            ]
        ];
    }
}
