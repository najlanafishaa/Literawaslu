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
        'late_days',
        'fine_amount',
        'fine_status',
        'fine_type',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'late_days' => 'integer',
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
    public function daysLate(?Carbon $compareDate = null): int
    {
        $compareDate = $compareDate
            ? Carbon::parse($compareDate)
            : ($this->return_date ? Carbon::parse($this->return_date) : Carbon::today());

        $dueDate = Carbon::parse($this->due_date);

        if ($compareDate->greaterThan($dueDate)) {
            return (int) max(0, $dueDate->copy()->startOfDay()->diffInDays($compareDate->copy()->startOfDay(), false));
        }

        return 0;
    }

    /**
     * Sync late penalty state based on current date.
     */
    public function syncLatePenaltyState(?Carbon $asOf = null): self
    {
        $asOf = $asOf ? Carbon::parse($asOf)->startOfDay() : Carbon::today()->startOfDay();
        $member = $this->member;
        $dueDate = Carbon::parse($this->due_date)->startOfDay();

        $daysLate = $asOf->greaterThan($dueDate)
            ? (int) $dueDate->copy()->diffInDays($asOf->copy(), false)
            : 0;
        $penaltyDaysToApply = max(0, $daysLate - (int) $this->late_days);

        if ($this->status === 'returned') {
            $this->late_days = max((int) $this->late_days, $daysLate);
            if ($daysLate >= 4) {
                $this->fine_status = $this->fine_status === 'paid' ? 'paid' : 'unpaid';
            } else {
                $this->fine_status = $this->fine_status === 'paid' ? 'paid' : 'none';
            }
            $this->save();

            return $this;
        }

        if ($daysLate > 0) {
            if ($member && $penaltyDaysToApply > 0 && $member->points > 0) {
                $deduction = 10 * $penaltyDaysToApply;
                $member->points = max(0, $member->points - $deduction);
                if ($member->points <= 0) {
                    $member->status = 'suspended';
                }
                $member->save();

                PointHistory::create([
                    'member_id' => $member->id,
                    'type' => 'deduct',
                    'points' => $deduction,
                    'description' => "Pengurangan poin keterlambatan peminjaman '{$this->book->title}' ({$penaltyDaysToApply} hari)",
                ]);
            }

            $this->status = 'terlambat';
            $this->fine_status = $daysLate >= 4 && $this->fine_status !== 'paid' ? 'unpaid' : 'none';
        } else {
            $this->status = 'borrowed';
            if ($this->fine_status === 'unpaid') {
                $this->fine_status = 'none';
            }
        }

        $this->late_days = $daysLate;
        $this->save();

        if ($member && ($daysLate > 0 || $member->points <= 0) && $member->points <= 0 && $member->status !== 'suspended') {
            $member->status = 'suspended';
            $member->save();
        }

        return $this;
    }

    public static function syncActiveBorrowStates(?Member $member = null, ?Carbon $asOf = null): void
    {
        $query = static::query()
            ->whereNull('return_date')
            ->whereIn('status', ['borrowed', 'terlambat']);

        if ($member) {
            $query->where('member_id', $member->id);
        }

        $query->get()->each(function (self $borrow) use ($asOf): void {
            $borrow->syncLatePenaltyState($asOf);
        });
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

    public function getCurrentStatusLabelAttribute(): string
    {
        if ($this->status === 'returned') {
            return 'Dikembalikan';
        }

        $daysLate = $this->daysLate();
        if ($daysLate > 0) {
            return 'Terlambat';
        }

        $today = Carbon::today()->startOfDay();
        $dueDate = Carbon::parse($this->due_date)->startOfDay();

        if ($today->equalTo($dueDate)) {
            return 'Akan Jatuh Tempo';
        }

        $diffDays = $today->diffInDays($dueDate, false);
        if ($diffDays <= 3) {
            return 'Akan Jatuh Tempo';
        }

        return 'Tepat Waktu';
    }

    public function getCurrentStatusKeyAttribute(): string
    {
        return match ($this->current_status_label) {
            'Dikembalikan' => 'returned',
            'Terlambat' => 'late',
            'Akan Jatuh Tempo' => 'upcoming',
            default => 'on_time',
        };
    }
}
