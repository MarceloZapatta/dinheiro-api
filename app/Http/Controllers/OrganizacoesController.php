<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizacaoResource;
use App\Http\Resources\OrganizacaoResourceCollection;
use App\Mensagem;
use App\Services\OrganizacoesService;
use Illuminate\Http\Request;
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

    /**
     * Mostra a organização
     */
    public function show(): \App\Http\Resources\OrganizacaoResource
    {
        return new OrganizacaoResource($this->organizacoesService->findPorHeader());
    }

    public function update(Request $request)
    {
        $this->organizacoesService->update($request);
        return response()->json(Mensagem::sucesso('Sucesso!'));
    }

    public function store(Request $request)
    {
        $this->organizacoesService->storeNova($request);
        return response()->json(Mensagem::sucesso('Sucesso!'));
    }

    public function aceitarConvite(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $this->organizacoesService->aceitarConvite($request->token);
        return response()->json(Mensagem::sucesso('Sucesso!'));
    }

    /**
     * Apaga a pessoa vincualada
     *
     * @param string $id
     * @return void
     */
    public function destroyPessoa($id)
    {
        $this->organizacoesService->deletePessoa($id);
        return response()->json(Mensagem::sucesso('Sucesso!'));
    }

    /**
     * Apaga o convite pendete
     *
     * @param string $id
     * @return void
     */
    public function destroyConvite($id)
    {
        $this->organizacoesService->deleteConvite($id);
        return response()->json(Mensagem::sucesso('Sucesso!'));
    }
}
