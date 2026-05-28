<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditCardInvoiceRequest;
use App\Http\Resources\CreditCardInvoiceResource;
use App\Models\Mensagem;
use App\Services\CreditCardInvoiceService;

/**
 * @group Credit Card Invoices
 *
 * Credit Card Invoices Management
 */
class CreditCardInvoiceController extends Controller
{
    public function __construct(private readonly CreditCardInvoiceService $invoiceService) {}

    /**
     * List credit card invoices
     *
     * @apiParam id int required Credit card account ID
     *
     * @param  string  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(string $id)
    {
        $invoices = $this->invoiceService->getInvoices((int) $id);

        return CreditCardInvoiceResource::collection($invoices);
    }

    /**
     * Store a new credit card invoice
     *
     * @apiParam id int required Credit card account ID
     * @bodyParam reference_date date required Data de referência da fatura
     * @bodyParam closing_date date required Data de fechamento da fatura
     * @bodyParam due_date date required Data de vencimento da fatura
     * @bodyParam amount float optional Valor total da fatura
     * @bodyParam is_paid boolean optional Se a fatura foi paga
     * @bodyParam paid_at date optional Data de pagamento
     *
     * @param  string  $id
     * @param  \App\Http\Requests\CreditCardInvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(string $id, CreditCardInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->store((int) $id, $request->validated());

        return response()->json(Mensagem::sucesso('Fatura criada com sucesso!', [
            'data' => new CreditCardInvoiceResource($invoice)
        ]), 201);
    }

    /**
     * Show a specific credit card invoice
     *
     * @apiParam id int required Credit card account ID
     * @apiParam invoiceId int required Invoice ID
     *
     * @param  string  $id
     * @param  string  $invoiceId
     * @return \Illuminate\Http\Response
     */
    public function show(string $id, string $invoiceId)
    {
        $invoice = $this->invoiceService->find((int) $id, (int) $invoiceId);

        return new CreditCardInvoiceResource($invoice);
    }

    /**
     * Update a credit card invoice
     *
     * @apiParam id int required Credit card account ID
     * @apiParam invoiceId int required Invoice ID
     * @bodyParam reference_date date optional Data de referência da fatura
     * @bodyParam closing_date date optional Data de fechamento da fatura
     * @bodyParam due_date date optional Data de vencimento da fatura
     * @bodyParam amount float optional Valor total da fatura
     * @bodyParam is_paid boolean optional Se a fatura foi paga
     * @bodyParam paid_at date optional Data de pagamento
     *
     * @param  string  $id
     * @param  string  $invoiceId
     * @param  \App\Http\Requests\CreditCardInvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(string $id, string $invoiceId, CreditCardInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->update((int) $id, (int) $invoiceId, $request->validated());

        return response()->json(Mensagem::sucesso('Fatura atualizada com sucesso!', [
            'data' => new CreditCardInvoiceResource($invoice)
        ]));
    }

    /**
     * Delete a credit card invoice
     *
     * @apiParam id int required Credit card account ID
     * @apiParam invoiceId int required Invoice ID
     *
     * @param  string  $id
     * @param  string  $invoiceId
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id, string $invoiceId)
    {
        $this->invoiceService->delete((int) $id, (int) $invoiceId);

        return response()->json(Mensagem::sucesso('Fatura deletada com sucesso!'));
    }
}
