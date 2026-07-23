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
     * Get reviews by this member.
     */
    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    /**
     * Get point history records for this member.
     */
    public function pointHistories()
    {
        return $this->hasMany(PointHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get active borrows count.
     */
    public function activeBorrowCount(): int
    {
        return $this->borrows()->whereIn('status', ['pending', 'borrowed', 'terlambat'])->count();
    }

    /**
     * Check if member has reached maximum online borrow limit (3 books).
     */
    public function hasReachedBorrowLimit(): bool
    {
        return $this->activeBorrowCount() >= 3;
    }

    /**
     * Check if member has an active (unreturned) borrow.
     */
    public function hasActiveBorrow(): bool
    {
        return $this->hasReachedBorrowLimit();
    }

    /**
     * Check if member has any unpaid fines.
     */
    public function hasUnpaidFine(): bool
    {
        return $this->borrows()->where('fine_status', 'unpaid')->exists();
    }

    /**
     * Check if member can borrow a new book.
     * Returns true if no active borrows and no unpaid fines.
     */
    public function canBorrow(): bool
    {
        return !$this->hasActiveBorrow() && !$this->hasUnpaidFine();
    }

    /**
     * Get reason why member cannot borrow.
     */
    public function borrowBlockReason(): ?string
    {
        if ($this->hasUnpaidFine()) {
            return 'Anda memiliki denda yang belum dibayar. Selesaikan kewajiban terlebih dahulu.';
        }
        if ($this->hasActiveBorrow()) {
            return 'Anda masih memiliki buku yang sedang dipinjam. Kembalikan buku terlebih dahulu.';
        }
        return null;
    }

    /**
     * Get membership level details dynamically.
     */
    public function getMembershipDetailsAttribute()
    {
        $loans = $this->total_loans;

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
