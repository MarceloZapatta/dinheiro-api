<?php

namespace App\Imports;

use App\Movimentacao;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MovimentacoesImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $movimentacoes)
    {
        foreach ($movimentacoes as $movimentacao) {
            Movimentacao::create([
                'organizacao_id' => request()->organizacao_id,
                'movimentacao_importacao_id' => null
            ]);
        }
    }
}
