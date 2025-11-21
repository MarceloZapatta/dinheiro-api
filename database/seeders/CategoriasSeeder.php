<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        Categoria::factory()->count(9)->create([
            'user_id' => $user->id,
        ]);
        Categoria::factory()->create([
            'user_id' => $user->id,
            'nome' => 'Outros',
        ]);
    }
}
