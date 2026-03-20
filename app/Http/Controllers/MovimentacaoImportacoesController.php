<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportExcelRequest;
use App\Http\Requests\ImportImageRequest;
use App\Http\Requests\ImportOfxRequest;
use App\Http\Resources\MovimentacaoImportacaoResourceCollection;
use App\Http\Resources\MovimentacaoImportacaoShow;
use App\Models\Mensagem;
use App\Services\MovimentacaoImportacoesService;
use Illuminate\Http\JsonResponse;
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

    public function importarExcel(ImportExcelRequest $request)
    {
        $movimentacaoImportacao = $this->movimentacaoImportacaoService->importarExcel($request);

        return response()->json(Mensagem::sucesso('Sucesso ao realizar a importação!', [
            'data' => [
                'movimentacao_importacao' => $movimentacaoImportacao
            ]
        ]));
    }

    /**
     * Realiza a importação por código de barras
     *
     * @param Request $request
     * @return void
     */
    public function importarCodigoBarras(Request $request)
    {
        $this->validate($request, [
            'codigo_barras' => 'required|size:44'
        ]);

        $movimentacaoImportacao = $this->movimentacaoImportacaoService->importarCodigoBarras($request);

        return response()->json(Mensagem::sucesso('Sucesso ao realizar a importação!', [
            'data' => [
                'movimentacao_importacao' => $movimentacaoImportacao
            ]
        ]));
    }

    /**
     * Import OFX files
     *
     * @param ImportOfxRequest $request
     * @return JsonResponse
     */
    public function importOfx(ImportOfxRequest $request): JsonResponse
    {
        $movimentacaoImportacao = $this->movimentacaoImportacaoService->importOfx($request);

        return response()->json(Mensagem::sucesso('Sucesso ao realizar a importação!', [
            'data' => [
                'movimentacao_importacao' => $movimentacaoImportacao
            ]
        ]));
    }

    /**
     * Import image request using AI features
     *
     * @param ImportImageRequest $request
     * @return JsonResponse
     */
    public function importImage(ImportImageRequest $request): JsonResponse
    {
        $movimentacaoImportacao = $this->movimentacaoImportacaoService->importImage($request);

        return response()->json(Mensagem::sucesso('Sucesso ao realizar a importação!', [
            'data' => [
                'movimentacao_importacao' => $movimentacaoImportacao
            ]
        ]));
    }

    public function confirmAll(string $id)
    {
        $this->movimentacaoImportacaoService->confirmAllImport((int) $id);

        return response()->json(Mensagem::sucesso('Todas as importações foram confirmadas com sucesso!'));
    }
}
