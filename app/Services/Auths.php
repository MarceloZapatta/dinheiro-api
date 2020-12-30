<?php

namespace App\Services;

use App\EmailVerificacaoToken;
use App\Mail\VerificarEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Auths {
    /**
     * Users service
     *
     * @var \App\Services\Users
     */
    private $usersService;

    function __construct(Users $usersService)
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

    private function gerarUrlTokenVerificacaoEmail(User $user): string {
        $token = Hash::make(Str::random(32));

        EmailVerificacaoToken::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        return config('app.url') . '/verificar-email?token=' . $token;
    }
}