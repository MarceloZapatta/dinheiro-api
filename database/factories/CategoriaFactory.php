<?php

namespace Database\Factories;

use App\Models\Cor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categoria>
 */
class CategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->unique()->word(),
            'user_id' => User::factory(),
            'cor_id' => Cor::factory(),
            'icone' => $this->faker->word(),
        ];
    }
}
