<?php

namespace App\Http\Requests\AppraiseDictionary;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class AppraisalConstructionCompanyRequest extends FormRequest
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
            'address' => [
                'required',
                'max:250',
            ],
            'phone_number' => [
                'required',
                'max:250',
            ],
            'manager_name' => [
                'required',
                'max:250',
            ],
            'unit_price_m2' => [
                'required',
            ],
            'is_defaults' => [
                'required',
            ],
        ];
    }
}
