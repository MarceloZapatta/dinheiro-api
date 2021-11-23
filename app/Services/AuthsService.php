<?php

namespace App\Services;

use App\EmailVerificacaoToken;
use App\Mail\RecuperarSenha;
use App\Mail\VerificarEmail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthsService
{
    /**
     * Users service.
     *
     * @var \App\Services\UsersService
     */
    private $usersService;

    /**
     * Organizações service.
     *
     * @var \App\Services\OrganizacoesService
     */
    private $organizacoesService;

    /**
     * PessoasService
     *
     * @var \App\Services\PessoasService
     */
    private $pessoasService;

    /**
     * Consultor Service
     *
     * @var \App\Services\ConsultoresService
     */
    private $consultoresService;

    public function __construct(
        UsersService $usersService,
        OrganizacoesService $organizacoesService,
        ConsultoresService $consultoresService,
        PessoasService $pessoasService
    ) {
        $this->usersService = $usersService;
        $this->organizacoesService = $organizacoesService;
        $this->consultoresService = $consultoresService;
        $this->pessoasService = $pessoasService;
    }

    /**
     * Realiza um novo cadastro
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\User
     */
    public function cadastrar(Request $request): User
    {
        /**
         * @var ?\App\User
         */
        $user = null;
        $url = '';

        DB::transaction(function () use ($request, &$user, &$url) {
            $user = $this->usersService->store($request);
            $pessoa = $this->pessoasService->store($user, $request);

            $this->organizacoesService->store(
                $pessoa,
                (int) $request->organizacao_tipo_id === 1 ?
                    'Pessoal' : $request->nome_fantasia,
                $request->organizacao_tipo_id,
                $request->documento
            );

            if ($request->consultor) {
                $this->consultoresService->store($user, $request);
            }

            $url = $this->gerarUrlTokenVerificacaoEmail($user);
        });

        Mail::to($user)
            ->send(new VerificarEmail($user->nome, $url));

        return $user;
    }

    /**
     * Gera a URL Token para verificação do e-mail
     *
     * @param User $user
     * @return string
     */
    private function gerarUrlTokenVerificacaoEmail(User $user): string
    {
        $token = urlencode(Hash::make(Str::random(32)));

        $user->email_verificacao_token = $token;
        $user->save();

        return route('verificar-email', ['token' => $token]);
    }

    /**
     * Verificar se o token passado é válido
     *
     * @param Request $request
     * @return bool
     */
    public function verificarEmail(Request $request): bool
    {
        $user = User::where('email_verificacao_token', $request->token)
            ->first();

        if ($user) {
            $user->email_verificado = 1;
            $user->email_verified_at = Carbon::now();
            $user->email_verificacao_token = NULL;
            $user->save();

            return true;
        }

        return false;
    }

    public function recuperarSenha(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('email_verificado', 1)
            ->first();

        if ($user) {
            $token = urlencode(Hash::make(Str::random(32)));
            $user->email_verificacao_token = $token;
            $user->save();

            $url = config('app.front_url') . '/recuperar-senha/' . $token;

            Mail::to($user)
                ->send(new RecuperarSenha($user->nome, $url));
        }
    }

    /**
     * Verificar se o token passado é válido
     *
     * @param Request $request
     * @return bool
     */
    public function verificarRecuperarSenha(Request $request): bool
    {
        $user = User::where('email_verificacao_token', $request->token)
            ->first();

        if ($user) {
            $user->email_verificacao_token = Hash::make(Str::random(10));
            $user->senha = Hash::make($request->senha);
            $user->save();

            return true;
        }

        return false;
    }
}
