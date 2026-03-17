<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardInvoiceTransactionResource extends JsonResource
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
            'categoria' => [
                'id' => $this->categoria->id,
                'nome' => $this->categoria->nome,
                'icone' => $this->categoria->icone,
                'cor' => $this->categoria->cor
            ],
        ];
    }
}
