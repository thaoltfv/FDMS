<?php

namespace App\Http\Requests\AppraiseDictionary;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class AppraiseOtherInformationRequest extends FormRequest
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
                'max:250',
            ],
            'type' => [
                'required',
                'max:250',
            ],

        ];
    }
}
