<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'title',
        'author',
        'publisher',
        'year',
        'category',
        'description',
        'stock',
        'available_stock',
        'cover_image',
        'drive_link',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'year' => 'integer',
        'stock' => 'integer',
        'available_stock' => 'integer',
    ];

    /**
     * Get the borrowings for this book.
     */
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * Get reviews for this book.
     */
    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    /**
     * Get average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get total review count.
     */
    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}
