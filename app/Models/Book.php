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

    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null){
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null){
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating');
    }

    public function scopePopularBetween(Builder $query, $from = null, $to = null){
        return $query->withReviewsCount()
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null)
    {
        return $query->withAvgRating()
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
    
    public function scopePopularLastMonth(Builder $query){
        return $query->PopularBetween(now()->subMonth(), now())
        ->highestRated(now()->subMonth(), now())
        ->minReviews(3);
    }

    public function scopePopularLast6Months(Builder $query){
        return $query->PopularBetween(now()->subMonth(6), now())
        ->highestRated(now()->subMonth(6), now())
        ->minReviews(6);
    }

    public function scopeHighestRatedLastMonth(Builder $query){
        return $query->highestRated(now()->subMonth(), now())
        ->PopularBetween(now()->subMonth(), now())
        ->minReviews(3);
    }

    public function scopeHighestRatedLast6Months(Builder $query){
        return $query->highestRated(now()->subMonth(6), now())
        ->PopularBetween(now()->subMonth(6), now())
        ->minReviews(6);
    }


    protected static function booted(){
        static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
        static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));
    }
}
