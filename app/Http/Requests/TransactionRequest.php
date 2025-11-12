<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'categoria_id' => 'required|integer|exists:categorias,id',
            'data_transacao' => 'required|date_format:Y-m-d',
            'valor' => 'required|numeric',
        ];
    }
}
