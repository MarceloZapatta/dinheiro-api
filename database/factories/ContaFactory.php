<?php

namespace Database\Factories;

use App\Models\Cor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conta>
 */
class ContaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->word(),
            'user_id' => User::factory(),
            'saldo_inicial' => $this->faker->randomFloat(2, 0, 10000),
            'cor_id' => Cor::factory(),
            'icone' => $this->faker->word(),
        ];
    }
}
