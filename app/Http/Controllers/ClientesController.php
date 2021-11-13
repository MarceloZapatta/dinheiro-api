<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClienteResource;
use App\Http\Resources\ClienteResourceCollection;
use App\Mensagem;
use App\Rules\Cep;
use App\Rules\CpfCnpj;
use App\Services\ClientesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Clientes
 * 
 * Clientes
 */
class ClientesController extends Controller
{
    private $clientesService;

    public function __construct(ClientesService $clientesService)
    {
        $this->clientesService = $clientesService;
    }

    /**
     * Listagem
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'busca' => 'nullable|max:255'
        ]);
        
        return new ClienteResourceCollection($this->clientesService->get($request));
    }

    /**
     * Gravar
     *
     * @bodyParam nome string required Nome da cliente
     * @bodyParam icone string required Ícone da cliente
     * @bodyParam cor_id int required ID Cor da cliente
     * @bodyParam saldo_inicial float required Saldo inicial da cliente
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required|max:255',
            'documento' => ['required', new CpfCnpj(), Rule::unique('clientes')->where(function ($query) {
                return $query->where('organizacao_id', request()->organizacao_id);
            })],
            'email' => ['required', 'email', Rule::unique('clientes')->where(function ($query) {
                return $query->where('organizacao_id', request()->organizacao_id);
            })],
            'endereco.cep' => ['required', new Cep],
            'endereco.rua' => 'required|max:255',
            'endereco.complemento' => 'nullable|max:255',
            'endereco.numero' => 'required|max:255',
            'endereco.cidade' => 'required|max:255',
            'endereco.uf_id' => 'required|exists:ufs,id',
        ]);

        $cliente = $this->clientesService->store($request);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $cliente
        ]));
    }

    /**
     * Visualizar
     *
     * @apiParam id int required ID da Cliente
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cliente = $this->clientesService->find($id);

        return new ClienteResource($cliente);
    }

    /**
     * Atualizar
     *
     * @apiParam id int required ID da cliente
     * @bodyParam nome string optional Nome da cliente
     * @bodyParam icone string optional Ícone da cliente
     * @bodyParam cor_id int optional ID Cor da cliente
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nome' => 'required|max:255',
            'documento' => ['required', new CpfCnpj(), Rule::unique('clientes')->where(function ($query) use ($id) {
                return $query->where('organizacao_id', request()->organizacao_id)
                    ->where('id', '<>', $id);
            })],
            'email' => ['required', 'email', Rule::unique('clientes')->where(function ($query) use ($id) {
                return $query->where('organizacao_id', request()->organizacao_id)
                    ->where('id', '<>', $id);
            })],
            'endereco.cep' => ['required', new Cep],
            'endereco.rua' => 'required|max:255',
            'endereco.complemento' => 'nullable|max:255',
            'endereco.numero' => 'required|max:255',
            'endereco.cidade' => 'required|max:255',
            'endereco.uf_id' => 'required|exists:ufs,id',
        ]);

        $cliente = $this->clientesService->update($request, $id);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $cliente
        ]));
    }

    /**
     * Excluir
     *
     * @apiParam id int required ID da Cliente
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->clientesService->delete($id);

        return response()->json(Mensagem::sucesso('Sucesso!'));
    }
}
