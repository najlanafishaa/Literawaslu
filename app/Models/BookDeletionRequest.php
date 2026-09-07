<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookDeletionRequest extends Model
{
    protected $fillable = ['book_id', 'requested_by', 'status'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
