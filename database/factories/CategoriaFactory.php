<?php

namespace Database\Factories;

use App\Models\Cor;
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
            'cor_id' => Cor::factory(),
            'icone' => $this->faker->word(),
        ];
    }
}
