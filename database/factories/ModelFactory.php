<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\EmailVerificacaoToken;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(
    User::class,
    function (Faker $faker) {
        return array(
            'nome' => $faker->name,
            'email' => $faker->email,
            'password' => Hash::make('123456'),
            'ativo' => 1,
        );
    }
);

$factory->define(
    EmailVerificacaoToken::class,
    function (Faker $faker) {
        return array(
            'user_id' => factory('App\User')->create()->id,
            'token' => Hash::make(Str::random(32)),
        );
    }
);
