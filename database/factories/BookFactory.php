<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class BookFactory extends Factory
{

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'created_at' => $createdAt = fake()->dateTimeBetween('-2 years'),
            'updated_at' => fake()->dateTimeBetween($createdAt, 'now')
        ];
    }
}
