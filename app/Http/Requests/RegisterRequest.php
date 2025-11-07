<?php

namespace App\Http\Requests;

use App\Rules\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        $tipoPessoaFisica = 1;
        $tipoPessoaJuridica = 2;

        return [
            'nome' => 'required|max:255',
            'documento' => ["required_if:organizacao_tipo_id,$tipoPessoaJuridica", new CpfCnpj],
            'organizacao_tipo_id' => "required|in:$tipoPessoaFisica,$tipoPessoaJuridica",
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|max:255',
            'nome_fantasia' => "required_if:organizacao_tipo_id,$tipoPessoaJuridica|max:255",
            'consultor' => "required_if:organizacao_tipo_id,$tipoPessoaFisica|boolean",
            'consultor_resumo' => 'required_if:consultor,1|max:255'

        ];
    }
}
