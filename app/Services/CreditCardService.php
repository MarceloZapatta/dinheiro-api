<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Models\Conta;
use App\Models\CreditCardInvoice;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CreditCardService
{
    public function get()
    {
        return Conta::where('user_id', Auth::id())
            ->where('account_type', AccountType::CREDIT_CARD->value)
            ->get();
    }

    public function store(Request $request): Conta
    {
        $data = $request->only([
            'nome',
            'icone',
            'cor_id',
            'account_type',
            'closing_day',
            'due_day',
            'credit_limit',
        ]);

        $data['user_id'] = Auth::id();
        $data['account_type'] = AccountType::CREDIT_CARD->value;

        return DB::transaction(function () use ($data) {
            $conta = Conta::create($data);

            CreditCardInvoice::create([
                'user_id' => Auth::id(),
                'conta_id' => $conta->id,
                'reference_date' => now()->startOfMonth(),
                'closing_date' => now()->addMonth()->setDay($data['closing_day']),
                'due_date' => now()->addMonth()->setDay($data['due_day']),
            ]);

            return $conta;
        });
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'nome',
            'icone',
            'cor_id',
            'closing_day',
            'due_day',
            'credit_limit',
        ]);

        Conta::where('user_id', Auth::id())->where('id', $id)->where('account_type', AccountType::CREDIT_CARD->value)->update($data);
    }

    public function delete($id)
    {
        Helpers::flushCacheMovimentacoes();
        Cache::forget('contas.saldos_iniciais.' . request()->organizacao_id);
        return Conta::where('user_id', Auth::id())->where('id', $id)->where('account_type', AccountType::CREDIT_CARD->value)->delete();
    }

    public function find($id)
    {
        return Conta::where('user_id', Auth::id())->where('id', $id)->where('account_type', AccountType::CREDIT_CARD->value)->firstOrFail();
    }

    /**
     * Get invoices for a credit card account
     *
     * @param int $accountId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInvoices(int $accountId)
    {
        return CreditCardInvoice::where('user_id', Auth::id())
            ->where('conta_id', $accountId)
            ->orderBy('reference_date', 'desc')
            ->with('transactions')
            ->get();
    }

    /**
     * Find a specific invoice
     *
     * @param integer $invoiceId
     * @return CreditCardInvoice
     */
    public function findInvoice(int $invoiceId): CreditCardInvoice
    {
        return CreditCardInvoice::where('user_id', Auth::id())
            ->where('id', $invoiceId)
            ->with('transactions')
            ->firstOrFail();
    }
}
