<?php

namespace App\Http\Requests\BuildingPrice;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class BuildingPriceRequest extends FormRequest
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
            'unit_price_m2' => [
                'required',
            ],
            'effect_from' => [
                'required',
            ]
        ];
    }
}
