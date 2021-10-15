<?php

namespace App\Services;

use App\Organizacao;

class OrganizacoesService {
    /**
     * Grava a organização
     *
     * @param \App\Pessoa $pessoa
     * @param integer $organizacaoTipoId
     * @return void
     */
    public function store(\App\Pessoa $pessoa, string $nome, int $organizacaoTipoId, string $documento = null): \App\Organizacao
    {
        return Organizacao::create([
            'nome' => $nome,
            'pessoa_responsavel_id' => $pessoa->id,
            'organizacao_tipo_id' => $organizacaoTipoId,
            'documento' => $documento,
            'email' => $pessoa->user->email,
        ]);
    }
}
