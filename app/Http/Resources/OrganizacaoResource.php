<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizacaoResource extends JsonResource
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
            'hash' => $this->hash,
            'nome' => $this->nome,
            'email' => $this->email,
            'documento' => $this->documento,
            'tipo' => $this->organizacaoTipo,
            'pessoas' => $this->organizacaoPessoas ? $this->organizacaoPessoas->map(function ($organizacaoPessoa) {
                return [
                    'id' => $organizacaoPessoa->pessoa->user->id,
                    'nome' => $organizacaoPessoa->pessoa->user->nome,
                    'email' => $organizacaoPessoa->pessoa->user->email
                ];
            }) : [],
            'convites' => $this->organizacaoConvites ? $this->organizacaoConvites->map(function ($organizacaoConvite) {
                return [
                    'id' => $organizacaoConvite->id,
                    'email' => $organizacaoConvite->email
                ];
            }) : []
        ];
    }
}
