<?php

namespace App\Http\Controllers;

class AuthController {
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Recebe o token JWT
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'sucesso' => false,
                'status_codigo' => 401,
                'mensagem' => 'Login ou senha inválidos'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function sair()
    {
        auth('api')->logout();

        return response()->json([
            'sucesso' => true,
            'status_codigo' => 200,
            'mensagem' => 'Sucesso ao sair!'
        ]);
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
        return response()->json([
            'sucesso' => true,
            'status_codigo' => 200,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}