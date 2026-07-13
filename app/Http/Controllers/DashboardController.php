<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Member;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show role-specific dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (in_array($user->role, ['user', 'member'])) {
            // Member Dashboard
            $member = $user->member;
            
            // Get active borrowings
            $activeBorrows = Borrow::where('member_id', $member->id)
                ->where('status', 'borrowed')
                ->with('book')
                ->get();
                
            $totalBorrows = Borrow::where('member_id', $member->id)->count();
            
            $availableBooksCount = Book::where('is_available', true)->count();
            
            return view('dashboards.member', compact('member', 'activeBorrows', 'totalBorrows', 'availableBooksCount'));
        } else {
            // Admin/Petugas Dashboard Stats
            $totalBooks = Book::count();
            $borrowedBooks = Book::where('is_available', false)->count();
            $availableBooks = Book::where('is_available', true)->count();
            $totalMembers = Member::count();
            $totalTransactions = Borrow::count();
            
            // Overdue borrows
            $overdueCount = Borrow::where('status', 'borrowed')
                ->where('due_date', '<', now()->toDateString())
                ->count();

            // Unverified members awaiting approval
            $unverifiedMembers = Member::where('is_verified', false)->with('user')->orderBy('created_at', 'asc')->get();

            // Recent loans
            $recentBorrows = Borrow::with(['member.user', 'book'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Most borrowed books (grouping by book_id)
            $popularBooks = Borrow::select('book_id')
                ->selectRaw('count(book_id) as total')
                ->groupBy('book_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->with('book')
                ->get();

            if ($user->role === 'super_admin') {
                return view('dashboards.admin', compact(
                    'totalBooks', 'borrowedBooks', 'availableBooks', 
                    'totalMembers', 'totalTransactions', 'overdueCount', 
                    'recentBorrows', 'popularBooks', 'unverifiedMembers'
                ));
            } else {
                return view('dashboards.petugas', compact(
                    'totalBooks', 'borrowedBooks', 'availableBooks', 
                    'totalMembers', 'totalTransactions', 'overdueCount', 
                    'recentBorrows', 'unverifiedMembers'
                ));
            }
        }
    }
}
