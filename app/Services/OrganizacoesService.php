<?php

namespace App\Services;

use App\Organizacao;

class OrganizacoesService {
    /**
     * Grava a organização
     *
     * @param \App\User $user
     * @param integer $organizacaoTipoId
     * @return void
     */
    public function store(\App\User $user, string $nome, int $organizacaoTipoId): \App\Organizacao
    {
        return Organizacao::create([
            'nome' => $nome,
            'user_id' => $user->id,
            'organizacao_tipo_id' => $organizacaoTipoId
        ]);
    }
}
