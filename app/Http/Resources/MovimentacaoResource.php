<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimentacaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'descricao' => $this->descricao,
            'data_transacao' => $this->data_transacao,
            'valor' => $this->valor,
            'conta' => [
                'id' => $this->conta->id,
                'nome' => $this->conta->nome,
                'icone' => $this->conta->icone,
                'cor' => $this->conta->cor
            ],
            'movimentacao_relacao' => $this->movimentacaoRelacao ? [
                'id' => $this->movimentacaoRelacao->id,
                'conta' => [
                    'id' => $this->movimentacaoRelacao->conta->id,
                    'nome' => $this->movimentacaoRelacao->conta->nome,
                    'icone' => $this->movimentacaoRelacao->conta->icone,
                    'cor' => $this->movimentacaoRelacao->conta->cor
                ],
            ] : null,
            'categoria' => [
                'id' => $this->categoria->id,
                'nome' => $this->categoria->nome,
                'icone' => $this->categoria->icone,
                'cor' => $this->categoria->cor
            ],
        ];
    }
}
