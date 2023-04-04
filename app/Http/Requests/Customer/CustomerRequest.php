<?php

namespace App\Http\Requests\Customer;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class CustomerRequest extends FormRequest
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
            'phone' => [
                'required',
                'max:12',
            ],
            'status' => [
                'required',
                'in:active, inactive',
            ],
            'customer_picture' => [
                'max:250',
            ],
            'tax_code' => [
                'max:250',
            ],
            'address' => [
                'max:250',
            ],
            'created_by' => [
                'required',
                'max:250',
            ],
        ];
    }
}
