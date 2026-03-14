<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContaResource extends JsonResource
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
            'saldo' => (float) $this->saldo,
            'saldo_inicial' => (float) $this->saldo_inicial,
            'cor' => $this->cor,
            'icone' => $this->icone,
            'account_type' => $this->account_type,
            'closing_day' => $this->closing_day,
            'due_day' => $this->due_day,
            'credit_limit' => $this->credit_limit ? (float) $this->credit_limit : null,
        ];
    }
}
