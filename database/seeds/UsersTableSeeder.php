<?php

namespace Database\Seeds;

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
            array(
                'nome' => 'Api Test',
                'email' => 'test@login.com',
                'password' => Hash::make('123456'),
                'ativo' => 1,
            )
        );
    }
}
