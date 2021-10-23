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
        return Conta::where('organizacao_id', $request->organizacao_id)
            ->create($request->only([
                'nome',
                'icone',
                'cor_id'
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
