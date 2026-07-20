<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'fine_status',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    /**
     * Get the member who borrowed.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the book that was borrowed.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Calculate how many days late (from due_date to today or return_date).
     */
    public function daysLate(): int
    {
        $compareDate = $this->return_date
            ? Carbon::parse($this->return_date)
            : Carbon::today();

        $dueDate = Carbon::parse($this->due_date);

        if ($compareDate->greaterThan($dueDate)) {
            return (int) $compareDate->diffInDays($dueDate);
        }

        return 0;
    }

    /**
     * Calculate fine based on days late and late_fee setting.
     * Fine only applies after 3+ days overdue.
     */
    public function calculateFine(float $lateFeeSetting = 2000): float
    {
        $daysLate = $this->daysLate();
        if ($daysLate > 3) {
            return $daysLate * $lateFeeSetting;
        }
        return 0;
    }

    /**
     * Get fine status label in Bahasa Indonesia.
     */
    public function getFineStatusLabelAttribute(): string
    {
        return match($this->fine_status) {
            'unpaid' => 'Menunggu Pembayaran',
            'paid'   => 'Sudah Dibayar',
            default  => 'Tidak Ada Denda',
        };
    }
}
