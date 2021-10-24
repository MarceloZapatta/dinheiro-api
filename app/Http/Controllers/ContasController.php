<?php

namespace App\Http\Controllers;

use App\Mensagem;
use App\Services\ContasService;
use Illuminate\Http\Request;

/**
 * @group Contas
 * 
 * Contas
 */
class ContasController extends Controller
{
    private $contasService;

    public function __construct(ContasService $contasService)
    {
        $this->contasService = $contasService;
    }

    /**
     * Listagem
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            Mensagem::sucesso(
                'Sucesso!',
                [
                    'sucesso' => true,
                    'status_codigo' => 200,
                    'data' => $this->contasService->get()
                ]
            )
        );
    }

    /**
     * Gravar
     *
     * @bodyParam nome string required Nome da conta
     * @bodyParam icone string required Ícone da conta
     * @bodyParam cor_id int required ID Cor da conta
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'icone' => 'required',
            'cor_id' => 'required|exists:cores,id',
            'saldo' => 'required|numeric',
            'saldo_inicial' => 'required|numeric'
        ]);

        $conta = $this->contasService->store($request);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $conta
        ]));
    }

    /**
     * Visualizar
     *
     * @apiParam id int required ID da Conta
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conta = $this->contasService->find($id);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $conta
        ]));
    }

    /**
     * Atualizar
     *
     * @apiParam id int required ID da conta
     * @bodyParam nome string optional Nome da conta
     * @bodyParam icone string optional Ícone da conta
     * @bodyParam cor_id int optional ID Cor da conta
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nome' => 'nullable',
            'icone' => 'nullable',
            'cor_id' => 'nullable|exists:cores,id'
        ]);

        $conta = $this->contasService->update($request, $id);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $conta
        ]));
    }

    /**
     * Excluir
     *
     * @apiParam id int required ID da Conta
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->contasService->delete($id);

        return response()->json(Mensagem::sucesso('Sucesso!'));
    }
}
