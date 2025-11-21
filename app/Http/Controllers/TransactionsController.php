<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Requests\TransactionsRequest;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Resources\MovimentacaoResource;
use App\Http\Resources\MovimentacaoResourceCollection;
use App\Models\JunoLogs;
use App\Models\Mensagem;
use App\Services\MovimentacoesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Movimentações
 *
 * Movimentações
 */
class TransactionsController extends Controller
{
    public function __construct(private readonly MovimentacoesService $movimentacoesService) {}

    /**
     * Listagem
     */
    public function index(TransactionsRequest $request): MovimentacaoResourceCollection
    {
        return new MovimentacaoResourceCollection(
            $this->movimentacoesService->get($request),
            $this->movimentacoesService->getSaldo($request),
            $this->movimentacoesService->getSaldoPrevisto($request)
        );
    }

    /**
     * Gravar
     *
     * @bodyParam descricao string required Descricao da movimentacao
     * @bodyParam data_transacao string required DAta em que ocorreu a movimentacao
     * @bodyParam categoria_id int required ID Categoria da movimentacao
     * @bodyParam conta_id int required ID Conta da movimentacao
     * @bodyParam valor float required Valor da movimentacao
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionStoreRequest $request): JsonResponse
    {
        $movimentacao = $this->movimentacoesService->store($request);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $movimentacao
        ]));
    }

    /**
     * Visualizar
     *
     * @apiParam id int required ID da Movimentacao
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movimentacao = $this->movimentacoesService->find($id);

        return new MovimentacaoResource($movimentacao);
    }

    /**
     * Atualizar
     *
     * @bodyParam descricao string required Descricao da movimentacao
     * @bodyParam data_transacao string required Data em que ocorreu a movimentacao
     * @bodyParam categoria_id int required ID Categoria da movimentacao
     * @bodyParam conta_id int required ID Conta da movimentacao
     * @bodyParam valor float required Valor da movimentacao
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionStoreRequest $request, $id)
    {
        $movimentacao = $this->movimentacoesService->update($request, $id);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $movimentacao
        ]));
    }

    /**
     * Excluir
     *
     * @apiParam id int required ID da Movimentacao
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->movimentacoesService->delete($id);

        return response()->json(Mensagem::sucesso('Sucesso!'));
    }

    public function emitirCobranca(Request $request)
    {
        // $this->validarMovimentacao($request);

        $cobranca = $this->movimentacoesService->emitirCobranca($request);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $cobranca
        ]));
    }

    public function webhookJuno(Request $request)
    {
        JunoLogs::create([
            'dados' => json_encode($request->all()),
            'mensagem' => 'Web hook JUNO'
        ]);
    }
}
