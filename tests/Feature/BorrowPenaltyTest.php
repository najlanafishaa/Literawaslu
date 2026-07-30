<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowPenaltyTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_borrow_deducts_points_and_marks_as_late(): void
    {
        $user = User::create([
            'name' => 'Member Test',
            'email' => 'member@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'member',
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'member_code' => 'MEM-0000001',
            'total_loans' => 0,
            'points' => 30,
            'borrow_limit' => 1,
            'status' => 'active',
        ]);

        $book = Book::create([
            'barcode' => 'BK-001',
            'title' => 'Test Book',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'year' => 2024,
            'category' => 'Test',
            'description' => 'Test',
            'stock' => 1,
            'available_stock' => 1,
            'is_available' => true,
        ]);

        $borrow = Borrow::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrow_date' => now()->subDays(3),
            'due_date' => now()->subDays(1),
            'status' => 'borrowed',
            'late_days' => 0,
            'fine_amount' => 0,
            'fine_status' => 'none',
        ]);

        $borrow->syncLatePenaltyState();

        $this->assertSame(1, $borrow->fresh()->late_days);
        $this->assertSame(20, $member->fresh()->points);
        $this->assertSame('terlambat', $borrow->fresh()->status);
        $this->assertSame('none', $borrow->fresh()->fine_status);
        $this->assertSame('Terlambat', $borrow->fresh()->current_status_label);
    }

    public function test_day_four_late_requires_donation_and_suspends_when_points_are_empty(): void
    {
        $user = User::create([
            'name' => 'Member Test 2',
            'email' => 'member2@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'member',
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'member_code' => 'MEM-0000002',
            'total_loans' => 0,
            'points' => 20,
            'borrow_limit' => 1,
            'status' => 'active',
        ]);

        $book = Book::create([
            'barcode' => 'BK-002',
            'title' => 'Test Book 2',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'year' => 2024,
            'category' => 'Test',
            'description' => 'Test',
            'stock' => 1,
            'available_stock' => 1,
            'is_available' => true,
        ]);

        $borrow = Borrow::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrow_date' => now()->subDays(6),
            'due_date' => now()->subDays(4),
            'status' => 'borrowed',
            'late_days' => 0,
            'fine_amount' => 0,
            'fine_status' => 'none',
        ]);

        $borrow->syncLatePenaltyState();

        $this->assertSame(4, $borrow->fresh()->late_days);
        $this->assertSame('unpaid', $borrow->fresh()->fine_status);
        $this->assertSame(0, $member->fresh()->points);
        $this->assertSame('suspended', $member->fresh()->status);
    }

    public function test_continued_late_deducts_points_daily_until_suspended(): void
    {
        $user = User::create([
            'name' => 'Member Test 3',
            'email' => 'member3@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'member',
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'member_code' => 'MEM-0000003',
            'total_loans' => 0,
            'points' => 50,
            'borrow_limit' => 1,
            'status' => 'active',
        ]);

        $book = Book::create([
            'barcode' => 'BK-003',
            'title' => 'Test Book 3',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'year' => 2024,
            'category' => 'Test',
            'description' => 'Test',
            'stock' => 1,
            'available_stock' => 1,
            'is_available' => true,
        ]);

        $borrow = Borrow::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrow_date' => now()->subDays(10),
            'due_date' => now()->subDays(6),
            'status' => 'borrowed',
            'late_days' => 0,
            'fine_amount' => 0,
            'fine_status' => 'none',
        ]);

        $borrow->syncLatePenaltyState();

        $this->assertSame(6, $borrow->fresh()->late_days);
        $this->assertSame('unpaid', $borrow->fresh()->fine_status);
        $this->assertSame(0, $member->fresh()->points);
        $this->assertSame('suspended', $member->fresh()->status);
    }
}
