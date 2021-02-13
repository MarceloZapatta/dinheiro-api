<?php

namespace Tests\Unit;

use App\EmailVerificacaoToken;
use TestCase;
use App\Traits\TokenHeader;
use App\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use TokenHeader, DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoginTest()
    {
        factory('App\User')->create([
            'email' => 'test@login.com',
            'password' => Hash::make('123456')
        ]);

        $this->json('POST', '/v1/auth/login', [
            'email' => 'test@login.com',
            'senha' => '123456'
        ])
            ->seeJson([
                'sucesso' => true,
                'status_codigo' => 200,
            ])
            ->assertResponseStatus(200);

        $this->json('POST', '/v1/auth/login', [
            'email' => 'test@login.com',
            'password' => '123456'
        ])
            ->seeJson([
                'sucesso' => false,
                'status_codigo' => 422
            ])
            ->assertResponseStatus(422);

        $this->json('POST', '/v1/auth/login', [
            'email' => 'test@login.com',
            'senha' => 'zzzz',
            'token_type' => 'bearer'
        ])
            ->seeJson([
                'sucesso' => false,
                'status_codigo' => 401,
                'mensagem' => 'Login ou senha inválidos'
            ])
            ->assertResponseStatus(401);
    }

    public function testLogoutTest()
    {
        $data = [];

        $user = factory('App\User')->create([
            'email' => 'test@login.com',
            'password' => Hash::make('123456')
        ]);

        $this->post('/v1/auth/sair', $data, $this->tokenHeader($user))
            ->seeJson([
                'sucesso' => true,
                'status_codigo' => 200,
                'mensagem' => 'Sucesso ao sair!'
            ])
            ->assertResponseStatus(200);
    }

    public function testAtualizarTest()
    {
        $data = [];

        $this->post('/v1/auth/atualizar', $data, $this->tokenHeader())
            ->seeJson([
                'sucesso' => true,
                'status_codigo' => 200,
                'token_type' => 'bearer'
            ])
            ->assertResponseStatus(200);
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
            ])
            ->assertResponseStatus(200);
    }

    public function testCadastrar()
    {
        $data = [
            'nome' => 'Marcelo Zapatta',
            'email' => 'emailteste@email.com',
            'senha' => '123456'
        ];

        $this->post('/v1/auth/cadastrar', $data)
            ->seeJson([
                'sucesso' => true,
                'status_codigo' => 201,
            ])
            ->assertResponseStatus(201);

        $this->seeInDatabase('users', [
            'nome' => 'Marcelo Zapatta',
            'email' => 'emailteste@email.com'
        ]);

        $user = User::where('email', 'emailteste@email.com')->first();

        $this->seeInDatabase('email_verificacao_tokens', [
            'user_id' => $user->id
        ]);
    }

    public function testValidarTokenCadastrar()
    {
        $emailVerificacaoToken = factory('App\EmailVerificacaoToken')
            ->create();

        $data = [
            'token' => $emailVerificacaoToken->token
        ];

        $request = $this->post('/v1/auth/verificar-email', $data);

        $verificacaoMensagem = urlencode(
            'O e-mail foi verificado com sucesso! Você já pode acessar sua conta.'
        );

        $request->response
            ->assertRedirect(
                env('APP_FRONT_URL') . 
                '?sucesso=' . 
                $verificacaoMensagem
            );

        $this->notSeeInDatabase('email_verificacao_tokens', [
            'id' => $emailVerificacaoToken->id
        ]);

        $data = [
            'token' => 'token-fake'
        ];

        $request = $this->post('/v1/auth/verificar-email', $data);

        $verificacaoMensagem = urlencode(
            'Não foi possível verificar o token. Tente novamente.'
        );

        $request->response
            ->assertRedirect(
                env('APP_FRONT_URL') . 
                '?erro=' . 
                $verificacaoMensagem
            );
    }
}
