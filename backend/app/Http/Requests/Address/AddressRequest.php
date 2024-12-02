<?php

namespace App\Http\Requests\Address;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class AddressRequest extends FormRequest
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
            'province' => [
                'required',
                'max:250',
                'nullable',
            ],
            'district' => [
                'required',
                'max:250',
                'nullable',
            ],
            'street' => [
                'max:250',
                'nullable',
            ],
        ];
    }
}
