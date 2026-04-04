<?php

namespace Database\Seeders;

use App\Models\Conta;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::get();

        foreach ($users as $user) {
            Conta::factory()->create([
                'nome' => 'C6 Bank',
                'user_id' => $user->id,
            ]);
        }
    }
}
