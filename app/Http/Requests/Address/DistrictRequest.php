<?php

namespace App\Http\Requests\Address;
use App\Http\Requests\FormRequest;

class DistrictRequest extends FormRequest
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
            'province_id' => [
                'required',
            ]
        ];
    }
}
