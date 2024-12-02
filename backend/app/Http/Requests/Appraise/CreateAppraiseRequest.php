<?php

namespace App\Http\Requests\Appraise;

use App\Http\Requests\FormRequest;
use App\Rules\CertificateRule;
use App\Rules\DocumentRule;

/**
 * @property integer id
 */
class CreateAppraiseRequest extends FormRequest
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
            'document_num' => [new DocumentRule()],
            'certificate_num' => [new CertificateRule()],
        ];
    }
}
