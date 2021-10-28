<?php

namespace App\Services;

use App\Movimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MovimentacoesService {
    public function get()
    {
        return Movimentacao::get();
    }

    public function store(Request $request)
    {
        $request->merge([
            'data_transacao' => Carbon::createFromFormat('d/m/Y', $request->data_transacao)->format('Y-m-d'),
            'organizacao_id' => $request->organizacao_id,
            'saldo' => $request->saldo_inicial
        ]);

        return Movimentacao::create($request->only([
                'organizacao_id',
                'descricao',
                'observacoes',
                'valor',
                'data_transacao',
                'conta_id',
                'categoria_id'
            ]));
    }

    public function update(Request $request, $id)
    {
        $movimentacao = Movimentacao::where('organizacao_id', $request->organizacao_id)
            ->findOrFail($id);

        $request->merge([
            'data_transacao' => Carbon::createFromFormat('d/m/Y', $request->data_transacao)->format('Y-m-d'),
        ]);        

        $movimentacao->update($request->only([
            'descricao',
            'observacoes',
            'valor',
            'data_transacao',
            'conta_id',
            'categoria_id'
        ]));
    }

    public function delete($id)
    {
        return Movimentacao::where('organizacao_id', request()->organizacao_id)
            ->where('id', $id)
            ->delete();
    }

    public function find($id)
    {
        return Movimentacao::where('organizacao_id', request()->organizacao_id)
            ->findOrFail($id);
    }
}
