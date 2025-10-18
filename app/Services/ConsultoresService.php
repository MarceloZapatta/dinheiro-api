<?php

namespace App\Services;

use App\Models\Consultor;
use App\Helpers\Helpers;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultoresService
{
    /**
     * Armazena o consultor
     *
     * @param \App\Models\User $request
     * @param Request $request
     * @return \App\Models\Consultor|null
     */
    public function store(User $user, Request $request): ?\App\Models\Consultor
    {
        $request->merge([
            'usuario_id' => $user->id,
            'resumo' => $request->consultor_resumo
        ]);
        return Consultor::create($request->only(['resumo', 'usuario_id']));
    }
}
