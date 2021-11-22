<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovimentacaoImportacaoResourceCollection;
use App\Http\Resources\MovimentacaoImportacaoShow;
use App\Mensagem;
use App\Services\MovimentacaoImportacoesService;
use Illuminate\Http\Request;

/**
 * @group Movimentações
 * 
 * Movimentações
 */
class MovimentacaoImportacoesController extends Controller
{
    private $movimentacaoImportacaoService;

    public function __construct(MovimentacaoImportacoesService $movimentacaoImportacaoService)
    {
        $this->movimentacaoImportacaoService = $movimentacaoImportacaoService;
    }

    /**
     * Listagem
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new MovimentacaoImportacaoResourceCollection(
            $this->movimentacaoImportacaoService->get($request)
        );
    }

    /**
     * Mostra a importação
     *
     * @return void
     */
    public function show($id)
    {
        return new MovimentacaoImportacaoShow(
            $this->movimentacaoImportacaoService->show($id)
        );
    }

    public function importarExcel(Request $request)
    {
        $this->validate($request, [
            'arquivo' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel|max:10240'
        ]);

        $movimentacaoImportacao = $this->movimentacaoImportacaoService->importarExcel($request);

        return response()->json(Mensagem::sucesso('Sucesso ao realizar a importação!', [
            'data' => [
                'movimentacao_importacao' => $movimentacaoImportacao
            ]
        ]));
    }
}
