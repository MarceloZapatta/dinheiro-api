<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MovimentacaoResourceCollection extends ResourceCollection
{
    private $saldo;
    private $saldoPrevisto;

    function __construct($resource, float $saldo, float $saldoPrevisto)
    {
        parent::__construct($resource);
        $this->saldo = $saldo;
        $this->saldoPrevisto = $saldoPrevisto;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'saldo' => $this->saldo,
                'saldo_previsto' => $this->saldoPrevisto
            ],
        ];
    }
}
