<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'description',
        'published_at',
        'category_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'date',
    ];

    /**
     * Get the borrow records associated with the book.
     *
     * This method defines a one-to-many relationship between the Book and BorrowRecord models.
     * Each book can have multiple borrow records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    /**
     * Get the ratings associated with the book.
     *
     * This method defines a one-to-many relationship between the Book and Rating models.
     * Each book can have multiple ratings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the categories associated with the book.
     *
     * This method defines a one-to-many relationship between the Book and Category models.
     * Each book can have multiple ratings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Calculate the average rating for the book.
     *
     * This method calculates the average of all ratings associated with the book.
     * If there are no ratings, it returns null.
     *
     * @return float|null
     */
    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    /**
     * Get the most recent borrow record for the book.
     *
     * This method returns the latest borrow record for the book, which can be useful
     * for tracking the last time the book was borrowed.
     *
     * @return \App\Models\BorrowRecord|null
     */
    public function latestBorrowRecord()
    {
        return $this->borrowRecords()->latest()->first();
    }

    /**
     * Get the most recent rating for the book.
     *
     * This method returns the latest rating for the book, which can be useful
     * for displaying recent user feedback.
     *
     * @return \App\Models\Rating|null
     */
    public function latestRating()
    {
        return $this->ratings()->latest()->first();
    }
}
