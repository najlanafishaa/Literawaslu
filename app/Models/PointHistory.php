<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'type',
        'points',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
