<?php

namespace App\Services;

use App\Categoria;
use App\Organizacao;
use Illuminate\Http\Request;

class CategoriasService
{
    public function get()
    {
        return Categoria::where('organizacao_id', request()->organizacao_id)
            ->get();
    }

    public function store(Request $request)
    {
        $request->merge([
            'organizacao_id' => $request->organizacao_id,
            'saldo' => $request->saldo_inicial
        ]);

        return Categoria::create($request->only([
            'organizacao_id',
            'nome',
            'icone',
            'cor_id',
            'saldo',
            'saldo_inicial'
        ]));
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::where('organizacao_id', $request->organizacao_id)
            ->findOrFail($id);
        $categoria->update($request->only([
            'nome',
            'icone',
            'cor_id'
        ]));
    }

    public function delete($id)
    {
        return Categoria::where('organizacao_id', request()->organizacao_id)
            ->where('id', $id)->delete();
    }

    public function find($id)
    {
        return Categoria::where('organizacao_id', request()->organizacao_id)
            ->findOrFail($id);
    }

    public function storeCategoriasIniciais(Organizacao $organizacao)
    {
        $categorias = [
            [
                'nome' => 'Alimentação',
                'icone' => 'fastFood',
                'cor_id' => 3,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Pagamentos',
                'icone' => 'calculator',
                'cor_id' => 8,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Doações',
                'icone' => 'heart',
                'cor_id' => 9,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Lazer',
                'icone' => 'beer',
                'cor_id' => 4,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Esportes',
                'icone' => 'bicycle',
                'cor_id' => 5,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Viagens',
                'icone' => 'airplane',
                'cor_id' => 1,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Vestuário',
                'icone' => 'bagHandle',
                'cor_id' => 10,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Outros',
                'icone' => 'logoAndroid',
                'cor_id' => 6,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Saúde',
                'icone' => 'bagHandle',
                'cor_id' => 10,
                'organizacao_id' => $organizacao->id
            ],
            [
                'nome' => 'Salário',
                'icone' => 'rocket',
                'cor_id' => 3,
                'organizacao_id' => $organizacao->id
            ]
        ];

        Categoria::insert($categorias);
    }
}
