<?php

namespace App\Http\Requests\AppraiseDictionary;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class AppraiserRequest extends FormRequest
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
            'appraiser_number' => [
                'required',
                'max:250',
            ],
            'appraise_position_id' => [
                'required',
            ]
        ];
    }
}
