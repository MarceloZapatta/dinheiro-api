<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Pessoa;
use App\User;
use Illuminate\Http\Request;

class PessoasService {
    /**
     * Armazena 
     *
     * @param \App\User $user
     * @param Request $request
     * @return \App\Pessoa|null
     */
    public function store(User $user, Request $request): ?\App\Pessoa
    {
        $requestMerge = [
            'usuario_id' => $user->id
        ];

        if ($request->documento) {
            $request['documento'] = Helpers::limparDocumento($request->documento);
        } else {
            $request->request->remove('documento');
        }

        $request->merge($requestMerge);

        return Pessoa::create($request->only(['documento', 'usuario_id']));
    }
}
