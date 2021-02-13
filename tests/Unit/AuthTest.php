<?php

namespace Tests\Unit;

use TestCase;
use App\Traits\TokenHeader;
use App\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use TokenHeader;
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoginTest()
    {
        factory('App\User')->create(
            array(
                'email' => 'test@login.com',
                'password' => Hash::make('123456'),
            )
        );

        $this->json(
            'POST',
            '/v1/auth/login',
            array(
                'email' => 'test@login.com',
                'senha' => '123456',
            )
        )
            ->seeJson(
                array(
                    'sucesso' => true,
                    'status_codigo' => 200,
                )
            )
            ->assertResponseStatus(200);

        $this->json(
            'POST',
            '/v1/auth/login',
            array(
                'email' => 'test@login.com',
                'password' => '123456',
            )
        )
            ->seeJson(
                array(
                    'sucesso' => false,
                    'status_codigo' => 422,
                )
            )
            ->assertResponseStatus(422);

        $this->json(
            'POST',
            '/v1/auth/login',
            array(
                'email' => 'test@login.com',
                'senha' => 'zzzz',
                'token_type' => 'bearer',
            )
        )
            ->seeJson(
                array(
                    'sucesso' => false,
                    'status_codigo' => 401,
                    'mensagem' => 'Login ou senha inválidos',
                )
            )
            ->assertResponseStatus(401);
    }

    public function testLogoutTest()
    {
        $data = array();

        $user = factory('App\User')->create(
            array(
                'email' => 'test@login.com',
                'password' => Hash::make('123456'),
            )
        );

        $this->post('/v1/auth/sair', $data, $this->tokenHeader($user))
            ->seeJson(
                array(
                    'sucesso' => true,
                    'status_codigo' => 200,
                    'mensagem' => 'Sucesso ao sair!',
                )
            )
            ->assertResponseStatus(200);
    }

    public function testAtualizarTest()
    {
        $data = array();

        $this->post('/v1/auth/atualizar', $data, $this->tokenHeader())
            ->seeJson(
                array(
                    'sucesso' => true,
                    'status_codigo' => 200,
                    'token_type' => 'bearer',
                )
            )
            ->assertResponseStatus(200);
    }

    public function testPerfil()
    {
        $data = array();

        $user = factory('App\User')->create();

        $this->post('/v1/auth/perfil', $data, $this->tokenHeader($user))
            ->seeJson(
                array(
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'ativo' => $user->ativo,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                )
            )
            ->assertResponseStatus(200);
    }

    public function testCadastrar()
    {
        $data = array(
            'nome' => 'Marcelo Zapatta',
            'email' => 'emailteste@email.com',
            'senha' => '123456',
        );

        $this->post('/v1/auth/cadastrar', $data)
            ->seeJson(
                array(
                    'sucesso' => true,
                    'status_codigo' => 201,
                )
            )
            ->assertResponseStatus(201);

        $this->seeInDatabase(
            'users',
            array(
                'nome' => 'Marcelo Zapatta',
                'email' => 'emailteste@email.com',
            )
        );

        $user = User::where('email', 'emailteste@email.com')->first();

        $this->seeInDatabase(
            'email_verificacao_tokens',
            array(
                'user_id' => $user->id,
            )
        );
    }

    public function testValidarTokenCadastrar()
    {
        $emailVerificacaoToken = factory('App\EmailVerificacaoToken')
            ->create();

        $data = array(
            'token' => $emailVerificacaoToken->token,
        );

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

        $this->notSeeInDatabase(
            'email_verificacao_tokens',
            array(
                'id' => $emailVerificacaoToken->id,
            )
        );

        $data = array(
            'token' => 'token-fake',
        );

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
