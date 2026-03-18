<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'descricao' => 'required|string|max:255',
            'conta_id' => 'required|integer|exists:contas,id',
            'conta_relacao_id' => 'nullable|integer|exists:contas,id',
            'categoria_id' => 'required_without:conta_relacao_id|nullable|integer|exists:categorias,id',
            'data_transacao' => 'required|date_format:Y-m-d',
            'valor' => 'required|numeric',
            'despesa' => 'required|boolean',
            'invoice_id' => 'nullable|integer|exists:credit_card_invoices,id',
        ];
    }
}
