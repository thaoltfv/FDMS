<?php

namespace App\Http\Requests\Apartment;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class ApartmentRequest extends FormRequest
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
            ],
            'ward_id' => [
                'required',
            ],
            'street_id' => [
                'required',
            ]

        ];
    }
}
