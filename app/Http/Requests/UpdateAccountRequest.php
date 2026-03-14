<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $accountId = $this->route('conta');

        $rules = [
            'nome' => [
                'required',
                'max:255',
                Rule::unique('contas', 'nome')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                })->ignore($accountId),
            ],
            'cor_id' => 'required|exists:cores,id',
            'account_type' => ['required', new Enum(AccountType::class)],
        ];

        if ($this->input('account_type') === AccountType::CREDIT_CARD->value) {
            $rules['closing_day'] = 'required|integer|min:1|max:31';
            $rules['due_day'] = 'required|integer|min:1|max:31';
            $rules['credit_limit'] = 'required|numeric|min:0';
        }

        return $rules;
    }
}
