<?php

namespace App\Services;

use App\Conta;
use App\Helpers\Helpers;
use App\Organizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContasService
{
    public function get()
    {
        return Conta::where('organizacao_id', request()->organizacao_id)
            ->get();
    }

    public function store(Request $request)
    {
        Helpers::flushCacheMovimentacoes();
        Cache::forget('contas.saldos_iniciais.' . $request->organizacao_id);
        $request->merge([
            'organizacao_id' => $request->organizacao_id,
        ]);
        return Conta::create($request->only([
            'organizacao_id',
            'nome',
            'icone',
            'cor_id',
            'saldo_inicial'
        ]));
    }

    public function update(Request $request, $id)
    {
        Helpers::flushCacheMovimentacoes();
        Cache::forget('contas.saldos_iniciais.' . $request->organizacao_id);
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
        Helpers::flushCacheMovimentacoes();
        Cache::forget('contas.saldos_iniciais.' . request()->organizacao_id);
        return Conta::where('organizacao_id', request()->organizacao_id)
            ->where('id', $id)->delete();
    }

    public function find($id)
    {
        return Conta::where('organizacao_id', request()->organizacao_id)
            ->findOrFail($id);
    }

    /**
     * Calcula o saldo inicial das contas
     *
     * @return float
     */
    public function calcularSaldosIniciais(): float
    {
        return Cache::rememberForever('contas.saldos_iniciais.' . request()->organizacao_id, function () {
            return Conta::where('organizacao_id', request()->organizacao_id)
                ->sum('saldo_inicial');
        });
    }

    public function storeContasIniciais(Organizacao $organizacao)
    {
        $contas = [
            [
                'nome' => 'Carteira',
                'icone' => 'wallet',
                'cor_id' => 3,
                'organizacao_id' => $organizacao->id,
                'saldo_inicial' => 0
            ],
            [
                'nome' => 'Conta corrente',
                'icone' => 'cash',
                'cor_id' => 7,
                'organizacao_id' => $organizacao->id,
                'saldo_inicial' => 0
            ],
            [
                'nome' => 'Conta poupança',
                'icone' => 'server',
                'cor_id' => 5,
                'organizacao_id' => $organizacao->id,
                'saldo_inicial' => 0
            ],
        ];

        Conta::insert($contas);
    }
}
