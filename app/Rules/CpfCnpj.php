<?php

namespace App\Rules;

use App\Helpers\ValidaCpfCnpj;
use Illuminate\Contracts\Validation\Rule;

class CpfCnpj implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validaCpfCnpj = new ValidaCpfCnpj($value);

        return $validaCpfCnpj->valida();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O :attribute é inválido.';
    }
}