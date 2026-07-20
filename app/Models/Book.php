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
}
