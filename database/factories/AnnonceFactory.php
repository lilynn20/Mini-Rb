<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Annonce>
 */
class AnnonceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'titre' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'adresse' => $this->faker->address,
            'ville' => $this->faker->city,
            'prix_par_nuit' => $this->faker->numberBetween(50, 500),
            'nombre_de_chambres' => $this->faker->numberBetween(1, 5),
            'image' => null,
        ];
    }
}
