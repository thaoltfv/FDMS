<?php

namespace App\Http\Requests\Dictionary;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class DictionaryRequest extends FormRequest
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
            'type' => [
                'required',
                'max:250',
            ],
            'description' => [
                'required',
                'max:250',
            ]
        ];
    }
}
