<?php

namespace App\Http\Requests\PreCertificateConfig;
/**
 * @property integer id
 */
class PreCertificateConfigRequest extends FormRequest
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
            'config' => [
                'required',
            ],
        ];
    }
}
