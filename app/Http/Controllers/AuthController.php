<?php

namespace App\Http\Controllers;

use App\Mensagem;
use App\Rules\CpfCnpj;
use App\Services\AuthsService;
use Illuminate\Http\Request;

/**
 * @group Auth
 * 
 * Autenticação do usuário
 */
class AuthController extends Controller
{
    /**
     * Auth Service.
     *
     * @var \App\Services\AuthsService
     */
    private $authsService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AuthsService $authsService)
    {
        $this->authsService = $authsService;
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Login
     * 
     * @bodyParam email string required O e-mail do usuário. Example: email@usuario.com
     * @bodyParam senha string required Senha do usuário Example: senha123
     * 
     * @response 200 {
     *   "sucesso": true,
     *   "status_codigo": 200,
     *   "access_token": "BEARER_TOKEN",
     *   "token_type": "bearer",
     *   "expires_in": 3600
     * }
     * 
     * @response 401 {
     *   "sucesso": false,
     *   "mensagem": "Login ou senha inválidos",
     *   "status_codigo": 401
     * }
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

        $credenciais = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credenciais)) {
            return response()->json(Mensagem::erro('Login ou senha inválidos', [], 401), 401);
        }

        $usuario = auth('api')->user();

        if (!$usuario->email_verificado) {
            auth('api')->invalidate();
            return response()->json(Mensagem::erro('O e-mail não foi verificado', [], 401), 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Registro
     *
     * @bodyParam nome string required Nome da pessoa
     * @bodyParam documento string obrigatório se Pessoa Júridica Example: Formato: XX.XXX.XXX/XXXX-XX
     * @bodyParam organizacao_tipo_id number required Indica se é PJ ou PF
     * @bodyParam email string required E-mail da pessoa
     * @bodyParam senha string required Senha da pessoa
     * @bodyParam nome_fantasia string optional Obrigatório quando organizacao_tipo_id = 2 (PJ)
     * @bodyParam consultor boolean required Indica se será um cadastro de consultor
     * @bodyParam consultor_resumo string optional Obrigatório se for cadastro de consultor
     * 
     * @param Request $request
     * @return void
     */
    public function cadastrar(Request $request)
    {
        $tipoPessoaFisica = 1;
        $tipoPessoaJuridica = 2;

        $this->validate(
            $request,
            [
                'nome' => 'required|max:255',
                'documento' => ["required_if:organizacao_tipo_id,$tipoPessoaJuridica", new CpfCnpj],
                'organizacao_tipo_id' => "required|in:$tipoPessoaFisica,$tipoPessoaJuridica",
                'email' => 'required|email|unique:usuarios,email|max:255',
                'senha' => 'required|max:255',
                'nome_fantasia' => "required_if:organizacao_tipo_id,$tipoPessoaJuridica|max:255",
                'consultor' => "required_if:organizacao_tipo_id,$tipoPessoaFisica|boolean",
                'consultor_resumo' => 'required_if:consultor,1|max:255'
            ]
        );

        $user = $this->authsService->cadastrar($request);

        if ($user) {
            return response()->json(Mensagem::sucesso('Sucesso!', [], 201), 201);
        }

        return response()->json(Mensagem::erro('Ocorreu um erro ao tentar criar o usuário.'));
    }

    /**
     * Verificar token de e-mail
     *
     * @bodyParam token string required Token recebido via e-mail 
     * 
     * @param Request $request
     * @return void
     */
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

        $verificacao = $this->authsService->verificarEmail($request);

        if ($verificacao) {
            $verificacaoMensagem = urlencode('O e-mail foi verificado com sucesso! Você já pode acessar sua conta.');

            return redirect(config('app.front_url') . '?sucesso=' . $verificacaoMensagem);
        }

        $verificacaoMensagem = urlencode('Não foi possível verificar o token. Tente novamente.');

        return redirect(config('app.front_url') . '?erro=' . $verificacaoMensagem);
    }

    /**
     * Esqueci a senha
     */
    public function esqueciSenha(Request $request)
    {
        $this->validate($request, [
            'email' => 'required'
        ]);

        $this->authsService->recuperarSenha($request);

        return response()->json(Mensagem::sucesso('Sucesso!', [], 200), 200);
    }


    public function verificarRecuperarSenha(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'senha' => 'required|max:255',
            'confirmar_senha' => 'required|same:senha'
        ]);

        $recuperarSenha = $this->authsService->verificarRecuperarSenha($request);

        if ($recuperarSenha) {
            return response()->json(Mensagem::sucesso('Senha recuperada com sucesso!', [], 200), 200);
        }
        
        return response()->json(Mensagem::sucesso('Ocorreu algum erro ao tentar recuperar a senha.', [], 400), 400);
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
                [
                    'sucesso' => true,
                    'status_codigo' => 200,
                    'data' => [
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => auth()->factory()->getTTL() * 60,
                    ]
                ]
            )
        );
    }
}
