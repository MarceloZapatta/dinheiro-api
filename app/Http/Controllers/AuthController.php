<?php

namespace App\Http\Controllers;

use App\Mensagem;
use App\Services\Auths;
use Illuminate\Http\Request;

class AuthController extends Controller {

    /**
     * Auth Service
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
     * Recebe o token JWT
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'senha' => 'required|max:255'
        ]);
        
        $request->merge([
            'password' => $request->senha
        ]);

        $credentials = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(Mensagem::erro('Login ou senha inválidos', [], 401), 401);
        }

        return $this->respondWithToken($token);
    }

    public function cadastrar(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'senha' => 'required|max:255'
        ]);

        $user = $this->authService->cadastrar($request);

        if ($user) {
            return response()->json(Mensagem::sucesso());
        }

        return response()->json(Mensagem::erro('Ocorreu um erro ao tentar criar o usuário.'));
    }

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
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json(Mensagem::sucesso('Sucesso!', [
            'sucesso' => true,
            'status_codigo' => 200,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]));
    }
}