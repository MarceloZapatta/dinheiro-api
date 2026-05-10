<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditCardRequest;
use App\Http\Resources\ContaResource;
use App\Http\Resources\ContaResourceCollection;
use App\Models\Mensagem;
use App\Services\CreditCardService;

/**
 * @group Accounts
 *
 * Accounts
 */
class CreditCardController extends Controller
{
    public function __construct(private readonly CreditCardService $creditCardService) {}

    /**
     * List credit cards
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ContaResourceCollection($this->creditCardService->get());
    }

    /**
     * Store credit card
     *
     * @bodyParam nome string required Nome do cartão
     * @bodyParam icone string required Ícone do cartão
     * @bodyParam cor_id int required ID Cor do cartão
     * @bodyParam saldo_inicial float required Saldo inicial do cartão
     * @bodyParam account_type string required Tipo de conta (bank, investment, credit_card)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreditCardRequest $request)
    {
        $account = $this->creditCardService->store($request);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $account
        ]));
    }

    /**
     * Show account
     *
     * @apiParam id int required Account ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = $this->creditCardService->find($id);

        return new ContaResource($account);
    }

    /**
     * Update credit card
     *
     * @apiParam id int required Credit card ID
     * @bodyParam nome string optional Nome do cartão
     * @bodyParam icone string optional Ícone do cartão
     * @bodyParam cor_id int optional ID Cor do cartão
     * @bodyParam account_type string required Account type (bank, investment, credit_card)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreditCardRequest $request, $id)
    {
        $account = $this->creditCardService->update($request, $id);

        return response()->json(Mensagem::sucesso('Sucesso!', [
            'data' => $account
        ]));
    }

    /**
     * Delete account
     *
     * @apiParam id int required Account ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->creditCardService->delete($id);

        return response()->json(Mensagem::sucesso('Sucesso!'));
    }
}
