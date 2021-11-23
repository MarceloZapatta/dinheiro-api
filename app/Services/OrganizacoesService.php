<?php

namespace App\Services;

use App\IntegracaoDado;
use App\Mail\ConviteOrganizacao;
use App\Organizacao;
use App\OrganizacaoConvite;
use App\OrganizacaoPessoa;
use App\Rules\Cep;
use App\Rules\CpfCnpj;
use App\Rules\Telefone;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrganizacoesService
{
    /**
     * Contas Service
     *
     * @var \App\Services\ContasService
     */
    private $contasService;

    /**
     * Categorias Service
     *
     * @var \App\Services\CategoriasService
     */
    private $categoriasService;

    public function __construct(
        ContasService $contasService,
        CategoriasService $categoriasService
    ) {
        $this->contasService = $contasService;
        $this->categoriasService = $categoriasService;
    }

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

    /**
     * Retorna a organização por hash
     *
     * @return \App\Organizacao
     */
    public function findPorHeader()
    {
        return Organizacao::where('id', request()->organizacao_id)
            ->first();
    }

    public function update(Request $request)
    {
        $organizacao = Organizacao::findOrFail(request()->organizacao_id);

        $organizacaoTipoPessoaJuridica = 2;

        if ($organizacao->organizacao_tipo_id === $organizacaoTipoPessoaJuridica) {
            $validationRules = [
                'nome' => 'required|max:255',
            ];
        } else {
            $validationRules = [
                'nome' => 'required|max:255',
                'email' => 'required|max:255',
                'documento' => ['nullable', new CpfCnpj],
                'email' => 'nullable|email',
                'razao_social' => 'nullable|max:255',
            ];
        }

        $validator = Validator::make($request->all(), $validationRules);
        $validator->validate();

        $organizacao = null;

        DB::transaction(function () use ($request, &$organizacao) {
            $request->merge([
                'documento' => str_replace(['.', '-', '/', ' '], '', $request->documento)
            ]);

            $organizacao = Organizacao::where('id', request()->organizacao_id)
                ->update($request->only([
                    'nome',
                    'email',
                    'documento',
                    'razao_social',
                ]));

            if ($request->convite_novos) {
                foreach ($request->convite_novos as $conviteNovo) {
                    $token = Hash::make(Str::random(10));
                    OrganizacaoConvite::create([
                        'organizacao_id' => request()->organizacao_id,
                        'email' => $conviteNovo,
                        'token' => $token
                    ]);
                    Mail::to($conviteNovo)
                        ->send(new ConviteOrganizacao($token));
                }
            }
        });

        return $organizacao;
    }

    public function storeNova(Request $request)
    {
        $organizacaoTipoPessoaFisica = 1;

        if ($request->organizacao_tipo_id === $organizacaoTipoPessoaFisica) {
            $validationRules = [
                'nome' => 'required|max:255',
            ];
        } else {
            $validationRules = [
                'organizacao_tipo_id' => 'required|exists:organizacao_tipos,id',
                'documento' => ['required', new CpfCnpj],
                'nome' => 'required|max:255',
                'razao_social' => 'nullable|max:255',
                'email' => 'nullable|max:255',
                'email' => 'nullable|email',
                'telefone' => ['nullable', new Telefone],
                'rua' => 'nullable|max:255',
                'numero' => 'nullable|max:255',
                'complemento' => 'nullable|max:255',
                'cidade' => 'nullable|max:255',
                'uf_id' => 'nullable|exists:ufs,id',
                'cep' => ['nullable', new Cep]
            ];
        }

        $validator = Validator::make($request->all(), $validationRules);
        $validator->validate();

        $organizacao = null;

        DB::transaction(function () use ($request, &$organizacao) {
            $user = auth('api')->user();
            $request->merge([
                'pessoa_responsavel_id' => $user->id,
                'documento' => str_replace(['.', '-', '/', ' '], '', $request->documento)
            ]);

            $organizacao = Organizacao::create($request->only([
                'organizacao_tipo_id',
                'pessoa_responsavel_id',
                'nome',
                'email',
                'documento',
                'nome',
                'email',
                'razao_social',
                'telefone',
                'rua',
                'numero',
                'complemento',
                'cidade',
                'uf_id',
                'cep'
            ]));

            OrganizacaoPessoa::create([
                'organizacao_id' => $organizacao->id,
                'pessoa_id' => $user->pessoa->id
            ]);

            $this->contasService->storeContasIniciais($organizacao);
            $this->categoriasService->storeCategoriasIniciais($organizacao);

            if ($request->convite_novos) {
                foreach ($request->convite_novos as $conviteNovo) {
                    $token = Hash::make(Str::random(10));
                    OrganizacaoConvite::create([
                        'organizacao_id' => $organizacao->id,
                        'email' => $conviteNovo,
                        'token' => $token
                    ]);
                    Mail::to($conviteNovo)
                        ->send(new ConviteOrganizacao($token));
                }
            }
        });

        return $organizacao;
    }

    /**
     * Aceita o convite
     *
     * @param string $token
     * @return void
     */
    public function aceitarConvite(string $token)
    {
        DB::transaction(function () use ($token) {
            $user = auth('api')->user();
            $convite = OrganizacaoConvite::where('token', urldecode($token))
                ->where('email', $user->email)
                ->firstOrFail();

            OrganizacaoPessoa::create([
                'organizacao_id' => $convite->organizacao_id,
                'pessoa_id' => $user->pessoa->id
            ]);

            $convite->delete();
        });
    }

    /**
     * Apaga a pessoa vinculada
     *
     * @param integer $id
     * @return void
     */
    public function deletePessoa(int $id)
    {
        if ($id === auth('api')->user()->pessoa->id) {
            abort(403, 'Acesso negado. Não é possível remover a pessoa responsável pela organização.');
        }

        return OrganizacaoPessoa::where('organizacao_id', request()->organizacao_id)
            ->where('pessoa_id', $id)
            ->where('pessoa_id', '<>', auth('api')->user()->pessoa->id)
            ->delete();
    }

    /**
     * Apaga o convite vinculado
     *
     * @param integer $id
     * @return void
     */
    public function deleteConvite(int $id)
    {
        return OrganizacaoConvite::where('organizacao_id', request()->organizacao_id)
            ->where('id', $id)
            ->delete();
    }

    /**
     * Recebe os dados de integração
     *
     * @return \Illuminate\Support\Collection
     */
    public function getIntegracaoDados(): \Illuminate\Support\Collection
    {
        return IntegracaoDado::get();
    }
}
