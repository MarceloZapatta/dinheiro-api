<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\ContaResource;
use App\Http\Resources\ContaResourceCollection;
use App\Models\Mensagem;
use App\Services\AccountsService;

/**
 * @group Accounts
 *
 * Accounts
 */
class AccountsController extends Controller
{
    public function __construct(private readonly AccountsService $accountsService) {}

    /**
     * List accounts
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ContaResourceCollection($this->accountsService->get());
    }

    /**
     * Store account
     *
     * @bodyParam nome string required Nome da conta
     * @bodyParam icone string required Ícone da conta
     * @bodyParam cor_id int required ID Cor da conta
     * @bodyParam saldo_inicial float required Saldo inicial da conta
     * @bodyParam account_type string required Account type (bank, investment, credit_card)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request)
    {
        $account = $this->accountsService->store($request);

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
        $account = $this->accountsService->find($id);

        return new ContaResource($account);
    }

    /**
     * Update account
     *
     * @apiParam id int required Account ID
     * @bodyParam nome string optional Nome da conta
     * @bodyParam icone string optional Ícone da conta
     * @bodyParam cor_id int optional ID Cor da conta
     * @bodyParam account_type string required Account type (bank, investment, credit_card)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, $id)
    {
        $account = $this->accountsService->update($request, $id);

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
        $this->accountsService->delete($id);

        return response()->json(Mensagem::sucesso('Sucesso!'));
    }
}
