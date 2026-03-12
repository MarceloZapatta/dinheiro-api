<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\User;
use App\Traits\DefaultCategory;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    use DefaultCategory;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        foreach ($this->getDefaultExpensesCategories() as $category) {
            $insertCategories[] = [
                'nome' => $category['nome'],
                'icone' => $category['icone'],
                'cor_id' => $category['cor_id'],
                'user_id' => $user->id,
                'expense' => true
            ];
        }

        foreach ($this->getDefaultIncomesCategories() as $category) {
            $insertCategories[] = [
                'nome' => $category['nome'],
                'icone' => $category['icone'],
                'cor_id' => $category['cor_id'],
                'user_id' => $user->id,
                'expense' => false
            ];
        }

        Categoria::insert($insertCategories);
    }
}
