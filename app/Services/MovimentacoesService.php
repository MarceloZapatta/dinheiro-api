<?php

namespace App\Services;

use App\Models\Cobranca;
use App\Helpers\Helpers;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Categoria;
use App\Models\JunoLogs;
use App\Models\Movimentacao;
use App\Models\MovimentacaoImportacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MovimentacoesService
{
    public function __construct(private readonly AccountsService $accountsService, private readonly CategoriasService $categoriasService) {}

    public function get(Request $request)
    {
        return Movimentacao::with('categoria')
            ->with('conta')
            ->with('movimentacaoRelacao')
            ->with('creditCardInvoice')
            ->whereNull('importacao_movimentacao_id')
            ->when($request->date_start, function ($query) use ($request) {
                $query->where('data_transacao', '>=', Carbon::parse($request->date_start));
            })
            ->when($request->date_end, function ($query) use ($request) {
                $query->where('data_transacao', '<=', Carbon::parse($request->date_end));
            })
            ->when(!empty($request->categorias), function ($query) use ($request) {
                $query->whereIn('categoria_id', $request->categorias);
            })
            ->when(!empty($request->contas), function ($query) use ($request) {
                $query->whereIn('conta_id', $request->contas);
            })
            ->where('user_id', Auth::id())
            ->orderBy('data_transacao', 'desc')
            ->get();
    }

    public function store(TransactionStoreRequest $request)
    {
        $userId = Auth::id();

        Helpers::flushCacheMovimentacoes();
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . Auth::id() . '.%');
        $request->merge([
            'user_id' => $userId
        ]);

        $despesa = (int) $request->despesa === 1;

        $handledValue = $despesa ? $this->transformarValorNegativo($request->valor) : $request->valor;

        $request->merge([
            'valor' => $handledValue
        ]);

        if ($request->conta_relacao_id) {
            return $this->handleTransferTransaction($request, $despesa);
        }

        return Movimentacao::create($request->only([
            'user_id',
            'descricao',
            'valor',
            'data_transacao',
            'conta_id',
            'categoria_id',
            'credit_card_invoice_id'
        ]));
    }

    private function handleTransferTransaction(Request $request, bool $despesa): Movimentacao
    {
        $userId = Auth::id();

        [$incomeTransferCategory, $outcomeTransferCategory] = $this->categoriasService->findTransferCategories();

        $movimentacaoDestino = Movimentacao::create([
            'user_id' => $userId,
            'descricao' => $request->descricao,
            'valor' => $request->valor * -1,
            'data_transacao' => $request->data_transacao,
            'conta_id' => $request->conta_relacao_id,
            'categoria_id' => $despesa ? $incomeTransferCategory->id : $outcomeTransferCategory->id
        ]);

        $request->merge([
            'movimentacao_relacao_id' => $movimentacaoDestino->id,
            'categoria_id' => $despesa ? $outcomeTransferCategory->id : $incomeTransferCategory->id
        ]);

        $movimentacao = Movimentacao::create($request->only([
            'user_id',
            'descricao',
            'valor',
            'data_transacao',
            'conta_id',
            'movimentacao_relacao_id',
            'categoria_id'
        ]));

        $movimentacaoDestino->update([
            'movimentacao_relacao_id' => $movimentacao->id
        ]);

        return $movimentacao;
    }

    public function update(Request $request, $id): Movimentacao
    {
        Helpers::flushCacheMovimentacoes();
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . Auth::id()  . '.%');
        $movimentacao = Movimentacao::where('user_id', Auth::id())
            ->findOrFail($id);

        $movimentacaoImportacaoId = $movimentacao->importacao_movimentacao_id;

        $request->merge([
            'importacao_movimentacao_id' => null,
            'data_transacao' => $request->data_transacao,
        ]);

        if ((int) $request->despesa === 1) {
            $request->merge([
                'valor' => $this->transformarValorNegativo($request->valor)
            ]);
        }

        if ($movimentacao->movimentacao_relacao_id) {
            $movimentacaoRelacionada = Movimentacao::find($movimentacao->movimentacao_relacao_id);

            if ($movimentacaoRelacionada) {
                $movimentacaoRelacionada->update([
                    'descricao' => $request->descricao,
                    'valor' => $request->valor * -1,
                    'data_transacao' => $request->data_transacao,
                ]);
            }

            $request->merge([
                'categoria_id' => $movimentacao->categoria_id
            ]);
        }

        $movimentacao->update($request->only([
            'importacao_movimentacao_id',
            'descricao',
            'observacoes',
            'valor',
            'data_transacao',
            'conta_id',
            'categoria_id',
            'credit_card_invoice_id'
        ]));

        if (
            $movimentacaoImportacaoId &&
            Movimentacao::where('importacao_movimentacao_id', $movimentacaoImportacaoId)
            ->count() <= 0
        ) {
            MovimentacaoImportacao::where('id', $movimentacaoImportacaoId)
                ->delete();
        }

        if ($movimentacao->installments_reference) {
            Movimentacao::where('user_id', Auth::id())
                ->where('installments_reference', $movimentacao->installments_reference)
                ->where('id', '!=', $movimentacao->id)
                ->update($request->only([
                    'importacao_movimentacao_id',
                    'descricao',
                    'observacoes',
                    'valor',
                    'conta_id',
                    'categoria_id',
                ]));
        }

        return $movimentacao;
    }

    public function delete($id): bool
    {
        $deleted = false;

        $movimentacao = Movimentacao::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($movimentacao->importacao_movimentacao_id) {
            $transactions = Movimentacao::where('user_id', Auth::id())
                ->where('id', $id)
                ->count();

            if ($transactions <= 1) {
                MovimentacaoImportacao::where('id', $movimentacao->importacao_movimentacao_id)
                    ->delete();
            }
        }

        $deleted = Movimentacao::where('user_id', Auth::id())
            ->whereIn('id', [$id, $movimentacao->movimentacao_relacao_id])
            ->delete();

        Helpers::flushCacheMovimentacoes();
        Helpers::flushCacheWildcard('movimentacoes.saldo_previsto.' . Auth::id() . '.%');

        return $deleted;
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
    private function transformarValorNegativo(float $valor): float
    {
        return abs($valor) * -1;
    }

    /**
     * Retona o saldo total das contas
     *
     * @return float
     */
    public function getSaldo(): float
    {
        return Cache::rememberForever('movimentacoes.saldo.' . Auth::id(), function () {
            $somaSaldosIniciais = $this->accountsService->calculateInitialBalances();
            $acumulado = (float) Movimentacao::where('data_transacao', '<=', Carbon::now())
                ->where('user_id', Auth::id())
                ->whereNull('importacao_movimentacao_id')
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
        return Cache::rememberForever('movimentacoes.saldo_previsto.' . Auth::id() . '.' . $request->data_fim, function () use ($request) {
            $somaSaldosIniciais = $this->accountsService->calculateInitialBalances();
            $acumulado = (float) Movimentacao::where('data_transacao', '<=', Carbon::parse($request->date_start))
                ->where('user_id', Auth::id())
                ->whereNull('importacao_movimentacao_id')
                ->sum('valor');

            return $acumulado + $somaSaldosIniciais;
        });
    }
}
