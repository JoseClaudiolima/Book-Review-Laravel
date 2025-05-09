<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        Book::factory(60)->create()->each(function ($book){
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
            ->good()
            ->for($book) //Association with the book column
            ->create();
        });

        Book::factory(60)->create()->each(function ($book){
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
            ->average()
            ->for($book) //Association with the book column
            ->create();
        });

        Book::factory(60)->create()->each(function ($book){
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
            ->bad()
            ->for($book) //Association with the book column
            ->create();
        });
    }
    
    
}
