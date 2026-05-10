<?php

namespace App\Services;

use App\Http\Requests\CreditCardInvoiceRequest;
use App\Models\CreditCardInvoice;
use Illuminate\Support\Facades\Auth;

class CreditCardInvoiceService
{
    /**
     * Get all invoices for a credit card account
     *
     * @param int $accountId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInvoices(int $accountId)
    {
        return CreditCardInvoice::where('user_id', Auth::id())
            ->where('conta_id', $accountId)
            ->orderBy('reference_date', 'desc')
            ->with(['transactions' => function ($query) {
                $query->orderBy('data_transacao', 'desc');
            }])
            ->get();
    }

    /**
     * Get a specific invoice
     *
     * @param int $accountId
     * @param int $invoiceId
     * @return CreditCardInvoice
     */
    public function find(int $accountId, int $invoiceId): CreditCardInvoice
    {
        return CreditCardInvoice::where('user_id', Auth::id())
            ->where('id', $invoiceId)
            ->where('conta_id', $accountId)
            ->with('transactions')
            ->firstOrFail();
    }

    /**
     * Create a new invoice
     *
     * @param int $accountId
     * @param CreditCardInvoiceRequest $request
     * @return CreditCardInvoice
     */
    public function store(int $accountId, CreditCardInvoiceRequest $request): CreditCardInvoice
    {
        $data = $request->validated();
        $data['is_paid'] = 0;
        $data['user_id'] = Auth::id();
        $data['conta_id'] = $accountId;

        return CreditCardInvoice::create($data);
    }

    /**
     * Update an invoice
     *
     * @param int $accountId
     * @param int $invoiceId
     * @param CreditCardInvoiceRequest $request
     * @return bool
     */
    public function update(int $accountId, int $invoiceId, CreditCardInvoiceRequest $request): bool
    {
        $data = $request->validated();

        return CreditCardInvoice::where('id', $invoiceId)
            ->where('conta_id', $accountId)
            ->where('user_id', Auth::id())
            ->update($data);
    }

    /**
     * Delete an invoice
     *
     * @param int $accountId
     * @param int $invoiceId
     * @return bool
     */
    public function delete(int $accountId, int $invoiceId): bool
    {
        $invoice = CreditCardInvoice::where('user_id', Auth::id())
            ->where('id', $invoiceId)
            ->where('conta_id', $accountId)
            ->firstOrFail();
        
        $invoice->transactions()->delete();

        return $invoice->delete();
    }
}
