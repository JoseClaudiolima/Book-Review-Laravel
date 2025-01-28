<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
     use HasFactory;

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title){
        // Function's name have to start with "scope" to be under Laravel pattern, and be allowed to be used as Local Query Scopes.
        // Also being used in php artisan tinker:
        // \App\Models\Book::title('delectus')->get();

        // Transform to sql sintax: ->toSql();
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopePopular(Builder $query){
        return $query->withCount('reviews')
        ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query){
        return $query->withAvg('reviews', 'rating')
        ->orderBy('reviews_avg_rating', 'desc');
    }
}
