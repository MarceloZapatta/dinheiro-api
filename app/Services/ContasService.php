<?php

namespace App\Services;

use App\Conta;
use Illuminate\Http\Request;

class ContasService {
    public function get()
    {
        return Conta::get();
    }

    public function store(Request $request)
    {
        $request->merge([
            'organizacao_id' => $request->organizacao_id,
            'saldo' => $request->saldo_inicial
        ]);

        return Conta::create($request->only([
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
        $conta = Conta::where('organizacao_id', $request->organizacao_id)
            ->findOrFail($id);
        $conta->update($request->only([
            'nome',
            'icone',
            'cor_id'
        ]));
    }

    public function delete($id)
    {
        return Conta::where('organizacao_id', request()->organizacao_id)
            ->where('id', $id)->delete();
    }

    public function find($id)
    {
        return Conta::where('organizacao_id', request()->organizacao_id)
            ->findOrFail($id);
    }
}
