<?php

namespace App\Providers;

use App\Categoria;
use App\Conta;
use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Validator::extend('conta_organizacao', function($attribute, $value, $parameters) {
            return Conta::where('organizacao_id', request()->organizacao_id)->find($value);
        });

        Validator::extend('categoria_organizacao', function($attribute, $value, $parameters) {
            return Categoria::where('organizacao_id', request()->organizacao_id)->find($value);
        });
    }
}
