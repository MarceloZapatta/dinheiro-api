<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Movimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MovimentacoesService
{
    private $contasService;

    public function __construct(ContasService $contasService)
    {
        $this->contasService = $contasService;
    }

    public function get(Request $request)
    {
        $movimentacoes = Movimentacao::orderBy('data_transacao');

        if ($request->data_inicio) {
            $movimentacoes->where('data_transacao', '>=', Carbon::createFromFormat('d/m/Y', $request->data_inicio));
        }

        if ($request->data_fim) {
            $movimentacoes->where('data_transacao', '<=', Carbon::createFromFormat('d/m/Y', $request->data_fim));
        }

        if (!empty($request->categorias)) {
            $movimentacoes->whereIn('categoria_id', $request->categorias);
        }

        if (!empty($request->contas)) {
            $movimentacoes->whereIn('conta_id', $request->contas);
        }

        return $movimentacoes->get();
    }

    public function store(Request $request)
    {
        Cache::forget('movimentacos.saldo.' . request()->organizacao_id);
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . request()->organizacao_id . '.%');
        $request->merge([
            'data_transacao' => Carbon::createFromFormat('d/m/Y', $request->data_transacao)->format('Y-m-d'),
            'organizacao_id' => $request->organizacao_id,
            'saldo' => $request->saldo_inicial
        ]);

        if ($request->despesa) {
            $request->merge([
                'valor' => $this->transformarValorNegativo($request->valor)
            ]);
        }

        return Movimentacao::create($request->only([
            'organizacao_id',
            'descricao',
            'observacoes',
            'valor',
            'data_transacao',
            'conta_id',
            'categoria_id'
        ]));
    }

    public function update(Request $request, $id)
    {
        Cache::forget('movimentacos.saldo.' . request()->organizacao_id);
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . request()->organizacao_id . '.%');
        $movimentacao = Movimentacao::where('organizacao_id', $request->organizacao_id)
            ->findOrFail($id);

        $request->merge([
            'data_transacao' => Carbon::createFromFormat('d/m/Y', $request->data_transacao)->format('Y-m-d'),
        ]);

        $movimentacao->update($request->only([
            'descricao',
            'observacoes',
            'valor',
            'data_transacao',
            'conta_id',
            'categoria_id'
        ]));
    }

    public function delete($id)
    {
        Cache::forget('movimentacos.saldo.' . request()->organizacao_id);
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . request()->organizacao_id . '.%');
        return Movimentacao::where('organizacao_id', request()->organizacao_id)
            ->where('id', $id)
            ->delete();
    }

    public function find($id)
    {
        return Movimentacao::where('organizacao_id', request()->organizacao_id)
            ->findOrFail($id);
    }

    /**
     * Retorna o valor negativo de um número
     *
     * @param float $valor
     * @return float
     */
    private function transformarValorNegativo(float $valor): float {
        return abs($valor) * -1;
    }

    /**
     * Retona o saldo total das contas
     *
     * @return float
     */
    public function getSaldo(): float
    {
        return Cache::rememberForever('movimentacos.saldo.' . request()->organizacao_id, function () {
            $somaSaldosIniciais = $this->contasService->calcularSaldosIniciais();
            $acumulado = (float) Movimentacao::where('data_transacao', '<=', Carbon::now())
                ->sum('valor');
                
            return $acumulado + $somaSaldosIniciais;
        });
    }

    /**
     * Retona o saldo total das contas
     *
     * @return float
     */
    public function getSaldoPrevisto(Request $request): float
    {
        return Cache::rememberForever('movimentacos.saldo_previsto.' . request()->organizacao_id . '.' . $request->data_fim, function () use ($request) {
            $somaSaldosIniciais = $this->contasService->calcularSaldosIniciais();
            $acumulado = (float) Movimentacao::where('data_transacao', '<=', Carbon::createFromFormat('d/m/Y', $request->data_fim))
                ->sum('valor');
            
            return $acumulado + $somaSaldosIniciais;
        });
    }
}
