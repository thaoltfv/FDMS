<?php

namespace App\Rules;

use App\Models\Appraise;
use Illuminate\Contracts\Validation\Rule;

class CertificateRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $result = Appraise::query()->where($attribute,'=',$value)->exists();
        if ($result){
            return false;
        } else return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Số Chứng Thư đã tồn tại. Vui lòng kiểm tra lại.');
    }
}
