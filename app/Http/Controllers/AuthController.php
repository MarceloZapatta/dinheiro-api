<?php

namespace App\Http\Controllers;

use App\Mensagem;
use App\Services\Auths;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Auth Service.
     *
     * @var \App\Services\Auths
     */
    private $authService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(Auths $authService)
    {
        $this->authService = $authService;
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Recebe o token JWT.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate(
            $request,
            array(
                'email' => 'required|email|max:255',
                'senha' => 'required|max:255'
            )
        );

        $request->merge(
            array(
                'password' => $request->senha,
            )
        );

        $credentials = request(array('email', 'password'));

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(Mensagem::erro('Login ou senha inválidos', array(), 401), 401);
        }

        return $this->respondWithToken($token);
    }

    public function cadastrar(Request $request)
    {
        $this->validate(
            $request,
            array(
                'nome' => 'required|max:255',
                'documento' => 'required',
                'organizacao_tipo_id' => 'required',
                'email' => 'required|email|unique:users,email|max:255',
                'senha' => 'required|max:255',
            )
        );

        $user = $this->authService->cadastrar($request);

        if ($user) {
            return response()->json(Mensagem::sucesso('Sucesso!', array(), 201), 201);
        }

        return response()->json(Mensagem::erro('Ocorreu um erro ao tentar criar o usuário.'));
    }

    public function verificarEmail(Request $request)
    {
        $messages = array(
            'token.required' => 'O token é inválido, tente novamente.',
            'token.max' => 'O token é inválido, tente novamente.',
        );

        $this->validate(
            $request,
            array(
                'token' => 'required|max:255',
            ),
            $messages
        );

        $verificacao = $this->authService->verificarEmail($request);

        if ($verificacao) {
            $verificacaoMensagem = urlencode('O e-mail foi verificado com sucesso! Você já pode acessar sua conta.');

            return redirect(env('APP_FRONT_URL') . '?sucesso=' . $verificacaoMensagem);
        }

        $verificacaoMensagem = urlencode('Não foi possível verificar o token. Tente novamente.');

        return redirect(env('APP_FRONT_URL') . '?erro=' . $verificacaoMensagem);
    }

    /**
     * Desloga o usuário
     *
     * @return \Illuminate\Http\Response
     */
    public function sair()
    {
        auth('api')->logout();

        return response()->json(Mensagem::sucesso('Sucesso ao sair!'));
    }

    public function atualizar()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function perfil()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json(
            Mensagem::sucesso(
                'Sucesso!',
                array(
                    'sucesso' => true,
                    'status_codigo' => 200,
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60,
                )
            )
        );
    }
}
