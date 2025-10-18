<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovimentacaoImportacaoShow extends JsonResource
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
            'movimentacoes' => $this->movimentacoes ? $this->movimentacoes->map(function ($movimentacao) {
                return new MovimentacaoResource($movimentacao);
            }) : []
        ];
    }
}
