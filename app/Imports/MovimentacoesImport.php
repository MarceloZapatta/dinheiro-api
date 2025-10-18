<?php

namespace App\Imports;

use App\Categoria;
use App\Conta;
use App\Movimentacao;
use App\MovimentacaoImport;
use App\MovimentacaoImportacao;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MovimentacoesImport implements ToCollection, WithHeadingRow
{
    function __construct(MovimentacaoImportacao $movimentacaoImportacao)
    {
        $this->movimentacaoImportacao = $movimentacaoImportacao;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $movimentacoes)
    {
        $contas = Conta::get();
        $categorias = Categoria::get();

        foreach ($movimentacoes as $movimentacao) {
            if (empty($movimentacao['descricao']) || empty($movimentacao['conta']) || empty($movimentacao['categoria']) || empty($movimentacao['valor']) || empty($movimentacao['data'])) {
                throw new Exception('Excel inválido.', 422);
            }

            $contaMovimentacao = $contas->where('nome', $movimentacao['conta'])->values();
            $contaMovimentacao = !empty($contaMovimentacao[0]) ? $contaMovimentacao[0] : $contas->values()[0];

            $categoriaMovimentacao = $categorias->where('nome', $movimentacao['categoria'])->values();
            $categoriaMovimentacao = !empty($categoriaMovimentacao[0]) ? $categoriaMovimentacao[0] : $categorias->values()[0];

            try {
                $dataTransacao = Carbon::createFromFormat('d/m/Y', $movimentacao['data']);
            } catch (\Throwable $th) {
                $dataTransacao = Carbon::now();
            }

            if ($movimentacao['tipo'] === 'Despesa') {
                $movimentacao['valor'] *= -1;
            }

            Movimentacao::create([
                'organizacao_id' => request()->organizacao_id,
                'importacao_movimentacao_id' => $this->movimentacaoImportacao->id,
                'descricao' => $movimentacao['descricao'],
                'observacoes' => $movimentacao['observacoes'],
                'conta_id' => $contaMovimentacao->id,
                'categoria_id' => $categoriaMovimentacao->id,
                'valor' => $movimentacao['valor'],
                'data_transacao' => $dataTransacao,
            ]);
        }
    }
}
