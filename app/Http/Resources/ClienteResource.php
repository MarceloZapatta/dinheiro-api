<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
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
            'nome' => $this->nome,
            'email' => $this->email,
            'documento' => $this->documento,
            'endereco' => [
                'cep' => $this->cep,
                'rua' => $this->rua,
                'complemento' => $this->complemento,
                'numero' => $this->numero,
                'cidade' => $this->cidade,
                'uf' => $this->uf
            ]
        ];
    }
}
