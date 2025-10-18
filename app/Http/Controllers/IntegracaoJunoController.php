<?php

namespace App\Http\Controllers;

use App\Services\IntegracaoJunoService;
use Illuminate\Http\Request;

class IntegracaoJunoController extends Controller
{
    private $integracaoJunoService;

    public function __construct(IntegracaoJunoService $integracaoJunoService)
    {
        $this->integracaoJunoService = $integracaoJunoService;
    }

    /**
     * Recebe o link de cadastro
     *
     * @return string
     */
    public function getLinkCadastro(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            [
                'data' => [
                    'url' => $this->integracaoJunoService->obterLinkCadastro()
                ]
            ]
        );
    }
}
