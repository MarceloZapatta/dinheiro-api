<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Models\Conta;
use App\Helpers\Helpers;
use App\Models\Organizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AccountsService
{
    public function get()
    {
        return Conta::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'nome',
            'icone',
            'cor_id',
            'saldo_inicial',
            'account_type',
        ]);

        if ($request->input('account_type') === AccountType::CREDIT_CARD->value) {
            $data['closing_day'] = $request->input('closing_day');
            $data['due_day'] = $request->input('due_day');
            $data['credit_limit'] = $request->input('credit_limit');
        }

        $data['user_id'] = Auth::id();

        return Conta::create($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'nome',
            'icone',
            'cor_id',
            'account_type',
        ]);

        if ($request->input('account_type') === AccountType::CREDIT_CARD->value) {
            $data['closing_day'] = $request->input('closing_day');
            $data['due_day'] = $request->input('due_day');
            $data['credit_limit'] = $request->input('credit_limit');
        } else {
            $data['closing_day'] = null;
            $data['due_day'] = null;
            $data['credit_limit'] = null;
        }

        Conta::where('id', $id)->updateOrFail($data);
    }

    public function delete($id)
    {
        Helpers::flushCacheMovimentacoes();
        Cache::forget('contas.saldos_iniciais.' . request()->organizacao_id);
        return Conta::where('id', $id)->delete();
    }

    public function find($id)
    {
        return Conta::findOrFail($id);
    }

    /**
     * Calculate initial balances for accounts
     *
     * @return float
     */
    public function calculateInitialBalances(): float
    {
        return Cache::rememberForever('contas.saldos_iniciais.' . request()->organizacao_id, function () {
            return Conta::where('user_id', Auth::id())
                ->sum('saldo_inicial');
        });
    }

    public function storeDefaultAccounts(Organizacao $organizacao)
    {
        $accounts = [
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

        Conta::insert($accounts);
    }
}
