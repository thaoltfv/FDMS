<?php

namespace App\Http\Requests\AppraiseDictionary;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class AppraiserCompanyRequest extends FormRequest
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
            'acronym' => [
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
            'fax_number' => [
                'required',
                'max:250',
            ],
            'appraiser_id' => [
                'required',
            ],
        ];
    }
}
