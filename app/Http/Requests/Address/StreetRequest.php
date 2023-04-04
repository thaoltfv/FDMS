<?php

namespace App\Http\Requests\Address;
use App\Http\Requests\FormRequest;

class StreetRequest extends FormRequest
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
            'district_id' => [
                'required',
            ],
            'province_id' => [
                'required',
            ]
        ];
    }
}
