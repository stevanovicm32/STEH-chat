<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Soba>
 */
class SobaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naziv' => fake()->words(2, true),
            'opis' => fake()->sentence(),
            'je_javna' => fake()->boolean(80), // 80% šanse da bude javna
            'maksimalan_broj_clanova' => fake()->numberBetween(10, 100),
        ];
    }
}
