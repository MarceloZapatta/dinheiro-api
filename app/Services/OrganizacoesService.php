<?php

namespace App\Services;

use App\Organizacao;
use App\OrganizacaoPessoa;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizacoesService {
    /**
     * Grava a organização
     *
     * @param \App\Pessoa $pessoa
     * @param integer $organizacaoTipoId
     * @return void
     */
    public function store(\App\Pessoa $pessoa, string $nome, int $organizacaoTipoId, string $documento = null): ?\App\Organizacao
    {
        $organizacao = null;

        DB::transaction(function () use (&$organizacao, $pessoa, $nome, $organizacaoTipoId, $documento) {
            $organizacao = Organizacao::create([
                'nome' => $nome,
                'pessoa_responsavel_id' => $pessoa->id,
                'organizacao_tipo_id' => $organizacaoTipoId,
                'documento' => $documento,
                'email' => $pessoa->user->email,
            ]);
    
            OrganizacaoPessoa::create([
                'organizacao_id' => $organizacao->id,
                'pessoa_id' => $pessoa->id
            ]);
        });

        return $organizacao;
    }

    /**
     * Recebe as organizações vinculadas ao usuário
     *
     * @param \App\User
     * @return \Illuminate\Support\Collection
     */
    public function getPorUsuario(User $user): \Illuminate\Support\Collection
    {
        return Organizacao::whereHas('organizacaoPessoas', function ($query) use ($user) {
            return $query->where('pessoa_id', $user->pessoa->id);
        })
            ->get();
    }
}
