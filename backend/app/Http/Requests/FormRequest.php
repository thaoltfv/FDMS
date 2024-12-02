<?php

namespace App\Http\Requests;

use App\Exceptions\FormValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class FormRequest extends LaravelFormRequest
{
    /**
     * @param Validator $validator
     * @throws FormValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new FormValidationException($validator);
    }
}
