<?php

namespace App\Http\Requests\AppraiseDictionary;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class AppraiseLawDocumentRequest extends FormRequest
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
            'document_type' => [
                'max:250',
            ],
            'date' => [
                'max:250',
            ],
            'content' => [
                'required',
            ],
            'provinces' => [
                'max:250',
            ],
            'position' => [
                'max:250',
            ]
        ];
    }
}
