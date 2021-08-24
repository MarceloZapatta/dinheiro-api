<?php

namespace App\Services;

use App\EmailVerificacaoToken;
use App\Mail\VerificarEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Auths
{
    /**
     * Users service.
     *
     * @var \App\Services\Users
     */
    private $usersService;

    /**
     * Organizações service.
     *
     * @var \App\Services\OrganizacoesService
     */
    private $organizacoesService;

    public function __construct(Users $usersService, OrganizacoesService $organizacoesService)
    {
        $this->usersService = $usersService;
        $this->organizacoesService = $organizacoesService;
    }

    /**
     * Realiza um novo cadastro
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\User
     */
    public function cadastrar(Request $request): User
    {
        DB::beginTransaction();
        
        $user = $this->usersService->store($request->nome, $request->email, $request->senha);
        $this->organizacoesService->store($user, $request->nome, $request->organizacao_tipo_id);
        $url = $this->gerarUrlTokenVerificacaoEmail($user);
        
        DB::commit();

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
        $token = Hash::make(Str::random(32));

        EmailVerificacaoToken::create(
            array(
                'user_id' => $user->id,
                'token' => $token,
            )
        );

        return config('app.url') . '/verificacao-email?token=' . $token;
    }

    /**
     * Verificar se o token passado é válido
     *
     * @param Request $request
     * @return bool
     */
    public function verificarEmail(Request $request): bool
    {
        $email = EmailVerificacaoToken::where('token', $request->token)
            ->first();

        if ($email) {
            $user = User::select('id', 'email_verificado')
                ->find($email->user_id);

            $user->email_verificado = 1;
            $user->save();
            $email->delete();

            return true;
        }

        return false;
    }
}
