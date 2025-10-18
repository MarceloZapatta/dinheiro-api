<?php

namespace App\Http\Controllers;

use App\Mensagem;
use App\Services\CoresService;

/**
 * @group Cores
 * 
 * Cores
 */
class CoresController extends Controller
{
    private $coresService;

    public function __construct(CoresService $coresService)
    {
        $this->coresService = $coresService;
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
                    'data' => $this->coresService->get()
                ]
            )
        );
    }
}
