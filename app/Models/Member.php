<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_code',
        'total_loans',
        'points',
        'borrow_limit',
        'status',
    ];

    protected $casts = [
        'total_loans' => 'integer',
        'points' => 'integer',
        'borrow_limit' => 'integer',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user account for this member.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the borrowings for this member.
     */
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * Get membership level details dynamically.
     */
    public function getMembershipDetailsAttribute()
    {
        $loans = $this->total_loans;

        // Use a uniform dark aesthetic for all member cards per requirements
        $cardBg = 'linear-gradient(135deg, var(--dark) 0%, #1a1a1a 100%)';
        $badgeBg = 'linear-gradient(135deg, var(--primary) 0%, #99131a 100%)';
        $badgeColor = '#FFFFFF';

        if ($loans >= 30) {
            return [
                'name' => 'Gold Member',
                'card_bg' => $cardBg,
                'badge_bg' => $badgeBg,
                'badge_color' => $badgeColor,
            ];
        } elseif ($loans >= 11) {
            return [
                'name' => 'Silver Member',
                'card_bg' => $cardBg,
                'badge_bg' => $badgeBg,
                'badge_color' => $badgeColor,
            ];
        } else {
            return [
                'name' => 'Bronze Member',
                'card_bg' => $cardBg,
                'badge_bg' => $badgeBg,
                'badge_color' => $badgeColor,
            ];
        }
    }
}
