<?php

namespace App\Http\Requests\CustomerGroup;

use App\Http\Requests\FormRequest;

/**
 * @property integer id
 */
class CustomerGroupFirstRequest extends FormRequest
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
            ]
        ];
    }
}
