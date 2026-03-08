<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meta>
 */
class MetaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "nome" => "Meta " . $this->faker->word(),
            "valor_objetivo" => $this->faker->randomFloat(2, 100, 1000),
            "ate_quando" => $this->faker->date()
        ];
    }
}
