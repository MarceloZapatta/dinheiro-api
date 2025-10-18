<?php

namespace App\Services;

use App\Models\Cor;

class CoresService
{
    /**
     * Retorna as cores
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): \Illuminate\Support\Collection
    {
        return Cor::get();
    }
}
