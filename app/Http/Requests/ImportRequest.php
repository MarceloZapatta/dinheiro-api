<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Exists;

class ImportRequest extends FormRequest
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
        return [
            'files' => 'required|array',
            'files.*' => 'file|mimes:ofx,application/x-ofx,jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
            'conta_id' => ['required', 'integer', new Exists('contas', 'id')->where('user_id', Auth::id())],
            'account_type' => ['required', 'string', new Enum(AccountType::class)],
            'credit_card_invoice_id' => ['required_if:account_type,' . AccountType::CREDIT_CARD->value, 'integer', 'exists:credit_card_invoices,id'],
        ];
    }
}
