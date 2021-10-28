<?php

namespace App\Services;

use App\Categoria;
use Illuminate\Http\Request;

class CategoriasService {
    public function get()
    {
        return Categoria::get();
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
}
