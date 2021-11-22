<?php

namespace App\Services;

use App\Cobranca;
use App\Helpers\Helpers;
use App\JunoLogs;
use App\Movimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MovimentacoesService
{
    private $contasService;
    private $junoService;

    public function __construct(ContasService $contasService, JunoService $junoService)
    {
        $this->contasService = $contasService;
        $this->junoService = $junoService;
    }

    public function get(Request $request)
    {
        $movimentacoes = Movimentacao::orderBy('data_transacao')
            ->with('cobranca');

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

        $movimentacoes->where('organizacao_id', request()->organizacao_id);

        return $movimentacoes->get();
    }

    public function store(Request $request)
    {
        Helpers::flushCacheMovimentacoes();
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . request()->organizacao_id . '.%');
        $request->merge([
            'data_transacao' => Carbon::createFromFormat('d/m/Y', $request->data_transacao)->format('Y-m-d'),
            'organizacao_id' => $request->organizacao_id,
            'saldo' => $request->saldo_inicial
        ]);

        if ((int) $request->despesa === 1) {
            $request->merge([
                'valor' => $this->transformarValorNegativo($request->valor)
            ]);
        }

        return Movimentacao::create($request->only([
            'organizacao_id',
            'cliente_id',
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
        Helpers::flushCacheMovimentacoes();
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . request()->organizacao_id . '.%');
        $movimentacao = Movimentacao::where('organizacao_id', $request->organizacao_id)
            ->findOrFail($id);

        $request->merge([
            'data_transacao' => Carbon::createFromFormat('d/m/Y', $request->data_transacao)->format('Y-m-d'),
        ]);

        $movimentacao->update($request->only([
            'cliente_id',
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
        $movimentacao = Movimentacao::where('organizacao_id', request()->organizacao_id)
            ->where('id', $id)
            ->firstOrFail();

        DB::transaction(function () use ($id, &$movimentacao) {            
            if ($movimentacao->cobranca) {
                $cancelarCobranca = $this->junoService->cancelarCobranca($movimentacao->cobranca);

                $movimentacao->cobranca->data_cancelamento = Carbon::now();
                $movimentacao->cobranca->status = 'canceled';
                $movimentacao->cobranca->save();
            } else {
                $movimentacao = Movimentacao::where('organizacao_id', request()->organizacao_id)
                    ->where('id', $id)
                    ->delete();
            }
        });

        Helpers::flushCacheMovimentacoes();
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . request()->organizacao_id . '.%');

        return $movimentacao;
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
        return Cache::rememberForever('movimentacoes.saldo.' . request()->organizacao_id, function () {
            $somaSaldosIniciais = $this->contasService->calcularSaldosIniciais();
            $acumulado = (float) Movimentacao::where('data_transacao', '<=', Carbon::now())
                ->where('organizacao_id', request()->organizacao_id)
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
        return Cache::rememberForever('movimentacoes.saldo_previsto.' . request()->organizacao_id . '.' . $request->data_fim, function () use ($request) {
            $somaSaldosIniciais = $this->contasService->calcularSaldosIniciais();
            $acumulado = (float) Movimentacao::where('data_transacao', '<=', Carbon::createFromFormat('d/m/Y', $request->data_fim))
                ->where('organizacao_id', request()->organizacao_id)
                ->sum('valor');
            
            return $acumulado + $somaSaldosIniciais;
        });
    }

    public function emitirCobranca(Request $request)
    {
        $movimentacao = $this->store($request);

        try {
            $cobranca = $this->junoService->emitirCobranca($movimentacao);
        } catch (\Throwable $th) {
            JunoLogs::create([
                'dados' => json_encode($movimentacao->toArray()) . '||' . $th->getMessage(),
                'message' => 'Falha na tentativa de gerar cobrança para a movimentação',
                'code' => $th->getCode()
            ]);

            throw $th;
        }

        $cobranca = Cobranca::create($this->junoService->gerarDataCobranca($movimentacao, $cobranca));
        return $cobranca;
    }
}
