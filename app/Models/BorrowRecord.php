<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'user_id',
        'borrowed_at',
        'due_date',
        'returned_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the user that borrowed the book.
     *
     * This method defines an inverse one-to-many relationship between 
     * BorrowRecord and User. Each borrow record belongs to a single user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that was borrowed.
     *
     * This method defines an inverse one-to-many relationship between 
     * BorrowRecord and Book. Each borrow record belongs to a single book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if the book has been returned.
     *
     * This method checks if the returned_at attribute is not null, 
     * indicating that the book has been returned.
     *
     * @return bool
     */
    public function isReturned()
    {
        return !is_null($this->returned_at);
    }
}
