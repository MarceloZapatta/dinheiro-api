<?php

use Andyabih\LaravelToUML\Http\Controllers\LaravelToUMLController;
use App\Http\Controllers\OrganizacoesController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get(
    '/',
    function () {
        return 'Poupis API v1.0.0';
    }
);

$router->group(
    array(
        'prefix' => 'v1',
    ),
    function ($router) {
        $router->group(
            array(
                'prefix' => 'auth',
            ),
            function ($router) {
                $router->post('login', 'AuthController@login');
                $router->post('cadastrar', 'AuthController@cadastrar');
                $router->get('verificar-email', ['as' => 'verificar-email', 'uses' => 'AuthController@verificarEmail']);
                $router->post('sair', 'AuthController@sair');
                $router->post('atualizar', 'AuthController@atualizar');
                $router->post('perfil', 'AuthController@perfil');
            }
        );

        $router->get('movimentacoes/integracoes/juno/webhook', 'MovimentacoesController@webhookJuno');

        $router->group(['middleware' => 'auth:api'], function ($router) {
            $router->get('organizacoes', 'OrganizacoesController@index');
            $router->post('organizacoes/convite', 'OrganizacoesController@aceitarConvite');
            $router->post('organizacoes', 'OrganizacoesController@store');

            $router->group(['middleware' => 'organizacao'], function ($router) {
                $router->get('cores', 'CoresController@index');
                $router->group(
                    [
                        'prefix' => 'contas'
                    ],
                    function ($router) {
                        $router->get('/', 'ContasController@index');
                        $router->post('/', 'ContasController@store');
                        $router->get('/{id}', 'ContasController@show');
                        $router->put('/{id}', 'ContasController@update');
                        $router->delete('/{id}', 'ContasController@destroy');
                    }
                );
                $router->group(
                    [
                        'prefix' => 'categorias'
                    ],
                    function ($router) {
                        $router->get('/', 'CategoriasController@index');
                        $router->post('/', 'CategoriasController@store');
                        $router->get('/{id}', 'CategoriasController@show');
                        $router->put('/{id}', 'CategoriasController@update');
                        $router->delete('/{id}', 'CategoriasController@destroy');
                    }
                );
                $router->group(
                    [
                        'prefix' => 'movimentacoes'
                    ],
                    function ($router) {
                        $router->group(
                            [
                                'prefix' => 'importacoes'
                            ],
                            function ($router) {
                                $router->get('/', 'MovimentacaoImportacoesController@index');
                                $router->post('/excel', 'MovimentacaoImportacoesController@importarExcel');
                                $router->get('/{id}', 'MovimentacaoImportacoesController@show');
                            }
                        );
                        $router->group(
                            [
                                'prefix' => 'dashboards'
                            ],
                            function ($router) {
                                $router->get('/por-categoria', 'DashboardsController@porCategoria');
                                $router->get('/movimentacoes-anual', 'DashboardsController@movimentacoesAnual');
                            }
                        );
                        $router->get('/', 'MovimentacoesController@index');
                        $router->post('/', 'MovimentacoesController@store');
                        $router->post('/emitir-cobranca', 'MovimentacoesController@emitirCobranca');
                        $router->get('/{id}', 'MovimentacoesController@show');
                        $router->put('/{id}', 'MovimentacoesController@update');
                        $router->delete('/{id}', 'MovimentacoesController@destroy');
                    }
                );
                $router->group(
                    [
                        'prefix' => 'clientes',
                        'middleware' => 'organizacaoPj'
                    ],
                    function ($router) {
                        $router->get('/', 'ClientesController@index');
                        $router->post('/', 'ClientesController@store');
                        $router->get('/{id}', 'ClientesController@show');
                        $router->put('/{id}', 'ClientesController@update');
                        $router->delete('/{id}', 'ClientesController@destroy');
                    }
                );
                $router->group(
                    [
                        'prefix' => 'organizacoes'
                    ],
                    function ($router) {
                        $router->get('/dados', 'OrganizacoesController@show');
                        $router->put('/', 'OrganizacoesController@update');
                        $router->group(
                            [
                                'middleware' => 'organizacaoResponsavel'
                            ],
                            function ($router) {
                                $router->delete('/pessoas/{id}', 'OrganizacoesController@destroyPessoa');
                                $router->delete('/convites/{id}', 'OrganizacoesController@destroyConvite');
                                $router->get('/integracoes/juno/link-cadastrar', 'IntegracaoJunoController@getLinkCadastro');
                            }
                        );
                        $router->delete('/{id}', 'OrganizacoesController@destroy');
                        $router->group(
                            [
                                'prefix' => 'integracoes'
                            ],
                            function ($router) {
                                $router->get('/', 'OrganizacoesController@integracoes');
                            }
                        );
                    }
                );
                $router->get('ufs', 'UfsController@index');
            });
        });
    }
);
