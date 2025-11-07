<?php

namespace App\Providers;

use App\Models\Categoria;
use App\Models\Conta;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
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
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('conta_organizacao', function ($attribute, $value, $parameters, $validator) {
            return Conta::where('organizacao_id', request()->organizacao_id)->find($value);
        });

        Validator::extend('categoria_organizacao', function ($attribute, $value, $parameters, $validator) {
            return Categoria::where('organizacao_id', request()->organizacao_id)->find($value);
        });
    }
}
