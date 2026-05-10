<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditCardInvoiceRequest extends FormRequest
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
            'reference_date' => 'required|date',
            'closing_date' => 'required|date|after_or_equal:reference_date',
            'due_date' => 'required|date|after_or_equal:closing_date',
        ];
    }

    public function messages(): array
    {
        return [
            'reference_date.required' => 'A data de referência é obrigatória',
            'closing_date.required' => 'A data de fechamento é obrigatória',
            'closing_date.after_or_equal' => 'A data de fechamento deve ser posterior ou igual à data de referência',
            'due_date.required' => 'A data de vencimento é obrigatória',
            'due_date.after_or_equal' => 'A data de vencimento deve ser posterior ou igual à data de fechamento',
        ];
    }
}
