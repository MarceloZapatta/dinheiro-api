<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizacaoResourceCollection;
use App\Services\OrganizacoesService;
use Illuminate\Support\Facades\Auth;

/**
 * @group Organizações
 * 
 * Organizações são entidades que podem ser vinculadas as Pessoas.
 */
class OrganizacoesController extends Controller
{
    /**
     * Organizacoes Service
     *
     * @var \App\Services\OrganizacoesService
     */
    private $organizacoesService;

    public function __construct(OrganizacoesService $organizacoesService)
    {
        $this->organizacoesService = $organizacoesService;
    }

    /**
     * Retorna as organizações do usuário
     *
     * @return \App\Http\Resources\OrganizacaoResourceCollection
     */
    public function index(): \App\Http\Resources\OrganizacaoResourceCollection
    {
        $user = Auth::user();
        return new OrganizacaoResourceCollection($this->organizacoesService->getPorUsuario($user));
    }
}
