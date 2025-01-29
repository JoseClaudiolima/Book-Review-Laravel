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

    // public function scopePopular(Builder $query){
    //     return $query->withCount('reviews')
    //     ->orderBy('reviews_count', 'desc');
    // }

    public function scopePopularBetween(Builder $query, $from = null, $to = null){
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null)
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating')
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews)
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }

    private function dateRangeFilter(Builder $q, $from = null, $to = null){
        if ($from && !$to){
            $q->where('created_at', '>=', $from);
        } else if (!$from && $to){
            $q->where('created_at', '<=', $to);
        } else if ($from && $to){
            $q->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopePopularPublished(Builder $query, $from = null){
        return $query->withCount('reviews')
        ->where('created_at', '>=', $from)
        ->orderBy('reviews_count', 'desc');
    }
}
