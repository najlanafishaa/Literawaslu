<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookReviewController extends Controller
{
    /**
     * Store a new review. Only members who have returned the book can review.
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
        ]);

        $member = Auth::user()->member;

        // Check if member has returned this book OR has borrowed it for 7 days or more
        $hasBorrowed = Borrow::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->where(function ($q) {
                $q->where('status', 'returned')
                  ->orWhere('borrow_date', '<=', now()->subDays(7));
            })
            ->exists();

        if (!$hasBorrowed) {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan untuk buku yang sudah pernah Anda kembalikan atau telah Anda pinjam selama minimal 7 hari.');
        }

        // Check if member already reviewed this book
        $existing = BookReview::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            // Update existing review
            $existing->update([
                'rating' => $request->rating,
                'review' => $request->review,
            ]);
            return back()->with('success', 'Ulasan Anda berhasil diperbarui.');
        }

        BookReview::create([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return back()->with('success', 'Ulasan Anda berhasil dikirim. Terima kasih!');
    }

    /**
     * Delete a review (admin or the reviewer).
     */
    public function destroy(BookReview $review)
    {
        $user = Auth::user();
        $member = $user->member;

        if ($user->role === 'super_admin' || $user->role === 'petugas' ||
            ($member && $member->id === $review->member_id)) {
            $review->delete();
            return back()->with('success', 'Ulasan berhasil dihapus.');
        }

        return back()->with('error', 'Anda tidak memiliki izin untuk menghapus ulasan ini.');
    }
}
