<?php

namespace App\Http\Requests\User;
use App\Http\Requests\FormRequest;
/**
 * @property integer id
 */
class BranchRequest extends FormRequest
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
                'required',
                'max:250',
            ],
            'address' => [
                'required',
                'max:250',
            ]
        ];
    }
}
