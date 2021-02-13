<?php

namespace App\Services;

use App\EmailVerificacaoToken;
use App\Mail\VerificarEmail;
use App\User;
use Illuminate\Http\Request;
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

    public function __construct(Users $usersService)
    {
        $this->usersService = $usersService;
    }

    public function cadastrar(Request $request)
    {
        $user = $this->usersService->store($request->nome, $request->email, $request->senha);
        $url = $this->gerarUrlTokenVerificacaoEmail($user);

        Mail::to($user)
            ->send(new VerificarEmail($user->nome, $url));

        return $user;
    }

    private function gerarUrlTokenVerificacaoEmail(User $user): string
    {
        $token = Hash::make(Str::random(32));

        EmailVerificacaoToken::create(
            array(
                'user_id' => $user->id,
                'token' => $token,
            )
        );

        return config('app.url') . '/verificar-email?token=' . $token;
    }

    public function verificarEmail(Request $request)
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
