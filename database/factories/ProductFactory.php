<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //

            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'image' => $this->faker->imageUrl(640, 480, 'products', true), // URL aléatoire pour une image
            'price' => $this->faker->randomFloat(2, 10, 1000), // Prix aléatoire entre 10 et 1000 avec 2 décimales
            'stock' => $this->faker->numberBetween(0, 100), // Stock aléatoire entre 0 et 100
            'created_at' => now(),
           'updated_at' => now(),
        ];


    }
}
