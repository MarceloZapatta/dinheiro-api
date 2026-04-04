<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [


                [
                    'nome' => 'Api Test',
                    'email' => 'test@login.com',
                    'password' => Hash::make('123456'),
                    'email_verificado' => 1,
                    'email_verified_at' => now(),
                    'ativo' => 1,
                ],
                [
                    'nome' => 'Marcelo Zapatta',
                    'email' => 'marcelozapatta0@gmail.com',
                    'password' => Hash::make('123456'),
                    'email_verificado' => 1,
                    'email_verified_at' => now(),
                    'ativo' => 1,
                ]
            ]
        );
    }
}
