<?php

namespace App\Http\Requests\CustomerGroup;

use App\Http\Requests\FormRequest;

/**
 * @property integer id
 */
class CustomerGroupThirdRequest extends FormRequest
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
            ],
            'second_id' => [
                'required',
            ]
        ];
    }
}
