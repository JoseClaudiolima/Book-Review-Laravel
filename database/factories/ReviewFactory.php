<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ReviewFactory extends Factory
{

    public function definition(): array
    {
        return [
            'book_id' => null,
            'review' => fake()->paragraph,
            'rating' => fake()->numberBetween(1,5),
            'created_at' => $createdAt = fake()->dateTimeBetween('-2 years'),
            'updated_at' => fake()->dateTimeBetween($createdAt, 'now')
        ];
    }
    
    public function good(){
        return $this->state(['rating' => fake()->numberBetween(4,5)]);
    }

    public function average(){
        return $this->state(['rating' => fake()->numberBetween(3,4)]);
    }

    public function bad(){
        return $this->state(['rating' => fake()->numberBetween(1,2)]);
    }
}
