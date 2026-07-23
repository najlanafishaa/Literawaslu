<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'rating',
        'review',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get review or comment attribute dynamically.
     */
    public function getReviewAttribute()
    {
        return $this->attributes['comment'] ?? $this->attributes['review'] ?? null;
    }
}
