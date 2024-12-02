<?php

namespace App\Http\Requests\Appraise;

use App\Http\Requests\FormRequest;
use App\Rules\CertificateRule;
use App\Rules\DocumentRule;
use App\Rules\UpdateCertificateRule;
use App\Rules\UpdateDocumentRule;

/**
 * @property integer id
 */
class UpdateAppraiseRequest extends FormRequest
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
            'document_num' => [new UpdateDocumentRule($this->id)],
            'certificate_num' => [new UpdateCertificateRule($this->id)],
        ];
    }
}
