<?php

namespace Tests\Unit;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use TestCase;
use App\Traits\ActingAs;
use App\Traits\TokenHeader;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use TokenHeader;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoginTest()
    {
        $this->json('POST', '/v1/auth/login', [
            'email' => 'test@login.com',
            'password' => '123456'
        ])
            ->seeJson([
                'sucesso' => true,
                'status_codigo' => 200
            ]);

        $this->json('POST', '/v1/auth/login', [
            'email' => 'test@login.com',
            'password' => 'zzzz',
            'token_type' => 'bearer'
        ])
            ->seeJson([
                'sucesso' => false,
                'status_codigo' => 401,
                'mensagem' => 'Login ou senha inválidos'
            ]);
    }

    public function testLogoutTest()
    {
        $data = [];

        $this->post('/v1/auth/sair', $data, $this->tokenHeader())
            ->seeJson([
                'sucesso' => true,
                'status_codigo' => 200,
                'mensagem' => 'Sucesso ao sair!'
            ]);
    }

    public function testAtualizarTest()
    {
        $data = [];

        $this->post('/v1/auth/atualizar', $data, $this->tokenHeader())
            ->seeJson([
                'sucesso' => true,
                'status_codigo' => 200,
                'token_type' => 'bearer'
            ]);
    }

    public function testPerfil()
    {
        $data = [];
        
        $user = factory('App\User')->create();

        $this->post('/v1/auth/perfil', $data, $this->tokenHeader($user))
            ->seeJson([
                'nome' => $user->nome,
                'email' => $user->email,
                'ativo' => $user->ativo,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]);
    }
}
