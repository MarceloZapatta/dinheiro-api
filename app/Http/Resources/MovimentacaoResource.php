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
            'conta' => [
                'id' => $this->conta->id,
                'nome' => $this->conta->nome,
                'icone' => $this->conta->icone,
                'cor' => $this->conta->cor
            ],
            'categoria' => [
                'id' => $this->categoria->id,
                'nome' => $this->categoria->nome,
                'icone' => $this->categoria->icone,
                'cor' => $this->categoria->cor
            ],
            'data_transacao' => Carbon::parse($this->data_transacao)->format('d/m'),
            'valor' => $this->valor
        ];
    }
}
