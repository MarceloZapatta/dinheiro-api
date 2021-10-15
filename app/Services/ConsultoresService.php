<?php

namespace App\Services;

use App\Consultor;
use App\Helpers\Helpers;
use App\Pessoa;
use App\User;
use Illuminate\Http\Request;

class ConsultoresService {
    /**
     * Armazena o consultor 
     *
     * @param \App\User $request
     * @param Request $request
     * @return \App\Consultor|null
     */
    public function store(User $user, Request $request): ?\App\Consultor
    {
        $request->merge([
            'usuario_id' => $user->id,
            'resumo' => $request->consultor_resumo
        ]);
        return Consultor::create($request->only(['resumo', 'usuario_id']));
    }
}
