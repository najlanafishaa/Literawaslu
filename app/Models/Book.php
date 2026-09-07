<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $hidden = [
        'drive_link',
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

    /**
     * Check if book is online (digital link available).
     */
    public function getIsOnlineAttribute(): bool
    {
        return !empty(trim($this->drive_link ?? ''));
    }
}
