<?php

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
    }
);
