<?php

namespace Database\Seeders;

use App\Models\Conta;
use App\Models\Cor;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conta::factory()->create([
            'nome' => 'C6 Bank',
            'user_id' => User::first()->id,
        ]);
    }
}
