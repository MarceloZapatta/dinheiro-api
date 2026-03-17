<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardInvoiceResource extends JsonResource
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
            'conta_id' => $this->conta_id,
            'reference_date' => $this->reference_date->format('Y-m-d'),
            'closing_date' => $this->closing_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'total_amount' => $this->getTotalAmount(),
            'is_paid' => $this->is_paid,
            'paid_at' => $this->paid_at?->format('Y-m-d'),
            'transactions' => new CreditCardInvoiceTransactionResourceCollection($this->whenLoaded('transactions')),
        ];
    }

    private function getTotalAmount()
    {
        return $this->transactions->sum('valor') * -1;
    }
}
