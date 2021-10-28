<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriaResource;
use App\Http\Resources\CategoriaResourceCollection;
use App\Mensagem;
use App\Services\CategoriasService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Categorias
 * 
 * Categorias
 */
class CategoriasController extends Controller
{
    private $categoriasService;

    public function __construct(CategoriasService $categoriasService)
    {
        $this->categoriasService = $categoriasService;
    }

    /**
     * Listagem
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CategoriaResourceCollection($this->categoriasService->get());
    }

    /**
     * Gravar
     *
     * @bodyParam nome string required Nome da categoria
     * @bodyParam icone string required Ícone da categoria
     * @bodyParam cor_id int required ID Cor da categoria
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required',
            'nome' => ['required', Rule::unique('categorias', 'nome')],
            'icone' => 'required',
            'cor_id' => 'required|exists:cores,id'
        ]);

        $categoria = $this->categoriasService->store($request);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $categoria
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
        $categoria = $this->categoriasService->find($id);

        return new CategoriaResource($categoria);
    }

    /**
     * Atualizar
     *
     * @apiParam id int required ID da categoria
     * @bodyParam nome string optional Nome da categoria
     * @bodyParam icone string optional Ícone da categoria
     * @bodyParam cor_id int optional ID Cor da categoria
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nome' => [
                'required', Rule::unique('categorias', 'nome')->ignore($id)
            ],
            'icone' => 'required',
            'cor_id' => 'required|exists:cores,id'
        ]);

        $categoria = $this->categoriasService->update($request, $id);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $categoria
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
        $this->categoriasService->delete($id);

        return response()->json(Mensagem::sucesso('Sucesso!'));
    }
}
