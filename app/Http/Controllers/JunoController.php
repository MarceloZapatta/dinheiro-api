<?php

namespace App\Http\Controllers;

use App\Services\OrganizacoesService;
use Illuminate\Http\Request;

/**
 * Juno
 */
class JunoController extends Controller
{
    private $organizacoesService;

    function __construct(OrganizacoesService $organizacoesService)
    {
        $this->organizacoesService = $organizacoesService;
    }

    /**
     * Cadastro do juno
     *
     * @return void
     */
    public function store(Request $request)
    {
        $this->organizacoesService->update($request);
        $this->junoService->
    }
}
