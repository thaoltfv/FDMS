<?php

namespace App\Rules;

use App\Models\Appraise;
use Illuminate\Contracts\Validation\Rule;

class UpdateDocumentRule implements Rule
{
    private $id;

    /**
     * Create a new rule instance.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $result = Appraise::query()
            ->where($attribute,'=',$value)
            ->where('id','!=', $this->id)
            ->exists();
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
        return __('Số CVĐ đã tồn tại. Vui lòng kiểm tra lại.');
    }
}
