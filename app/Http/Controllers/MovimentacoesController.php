<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovimentacaoResource;
use App\Http\Resources\MovimentacaoResourceCollection;
use App\Mensagem;
use App\Services\MovimentacoesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Movimentações
 * 
 * Movimentações
 */
class MovimentacoesController extends Controller
{
    private $movimentacoesService;

    public function __construct(MovimentacoesService $movimentacoesService)
    {
        $this->movimentacoesService = $movimentacoesService;
    }

    /**
     * Listagem
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'data_inicio' => 'nullable|date_format:d/m/Y',
            'data_fim' => 'nullable|date_format:d/m/Y'
        ]);

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
     * @bodyParam observacoes string optional Observações da movimentação
     * @bodyParam data_transacao string required DAta em que ocorreu a movimentacao
     * @bodyParam categoria_id int required ID Categoria da movimentacao
     * @bodyParam conta_id int required ID Conta da movimentacao
     * @bodyParam valor float required Valor da movimentacao
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'descricao' => 'required|max:255',
            'observacoes' => 'nullable|max:255',
            'valor' => 'required|numeric',
            'data_transacao' => 'required|date_format:d/m/Y',
            'conta_id' => 'required|numeric|conta_organizacao',
            'categoria_id' => 'required|numeric|categoria_organizacao'
        ], [
            'conta_organizacao' => 'A conta não foi encontrada',
            'categoria_organizacao' => 'A categoria não foi encontrada'
        ]);

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
     * @bodyParam observacoes string optional Observações da movimentação
     * @bodyParam data_transacao string required Data em que ocorreu a movimentacao
     * @bodyParam categoria_id int required ID Categoria da movimentacao
     * @bodyParam conta_id int required ID Conta da movimentacao
     * @bodyParam valor float required Valor da movimentacao
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'descricao' => 'required|max:255',
            'observacoes' => 'nullable|max:255',
            'valor' => 'required|numeric',
            'data_transacao' => 'required|date_format:d/m/Y',
            'conta_id' => 'required|numeric|conta_organizacao',
            'categoria_id' => 'required|numeric|categoria_organizacao'
        ], [
            'conta_organizacao' => 'A conta não foi encontrada',
            'categoria_organizacao' => 'A categoria não foi encontrada'
        ]);

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
}
