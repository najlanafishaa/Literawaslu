<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;

class MemberAdminController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $query = Member::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('member_code', 'like', "%{$search}%");
        }

        $members = $query->orderBy('created_at', 'desc')->get();
        return view('members.index', compact('members'));
    }

    /**
     * Show edit form for member.
     */
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update member details.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
            'borrow_limit' => 'required|integer|min:1|max:10',
            'role' => 'required|string|in:user,admin',
        ]);

        $user = $member->user;

        // Update User Details
        $user->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        // If role is changed to admin, delete member profile
        if ($request->role === 'admin') {
            $member->delete();
            return redirect()->route('members.index')->with('success', "Peran user '{$user->name}' berhasil diubah menjadi Admin.");
        }

        // Update Member Specific Data
        $member->update([
            'points' => $request->points,
            'borrow_limit' => $request->borrow_limit,
        ]);

        return redirect()->route('members.index')->with('success', "Informasi member '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Show the form for creating a new member manually.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created member manually.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'security_question' => 'required|string|max:255',
            'security_answer' => 'required|string|max:255',
        ], [
            'email.unique' => 'Email ini sudah digunakan.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'member',
            'security_question' => $request->security_question,
            'security_answer' => strtolower(trim($request->security_answer)),
        ]);

        // Generate member code
        do {
            $code = 'MEM-' . rand(100000, 999999);
        } while (Member::where('member_code', $code)->exists());

        $member = Member::create([
            'user_id' => $user->id,
            'member_code' => $code,
            'total_loans' => 0,
            'points' => 10,
            'borrow_limit' => 1,
            'status' => 'active', 
        ]);

        \App\Models\PointHistory::create([
            'member_id' => $member->id,
            'type' => 'earn',
            'points' => 10,
            'description' => 'Bonus Poin Registrasi Akun Baru',
        ]);

        return redirect()->route('members.index')->with('success', 'Member baru berhasil didaftarkan secara manual.');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Member $member)
    {
        $user = $member->user;
        
        // Check if member has active loans
        $activeLoansCount = $member->borrows()->where('status', 'borrowed')->count();
        if ($activeLoansCount > 0) {
            return back()->with('error', "Gagal menghapus member. Member '{$user->name}' sedang memiliki peminjaman aktif.");
        }

        $member->delete();
        $user->delete();

        return redirect()->route('members.index')->with('success', 'Data member berhasil dihapus dari sistem.');
    }

    /**
     * Verify member account.
     */
    public function verify(Member $member)
    {
        $member->update(['is_verified' => true]);
        return back()->with('success', "Member '{$member->user->name}' berhasil diverifikasi.");
    }

    /**
     * Reject and delete member account registration.
     */
    public function reject(Member $member)
    {
        $user = $member->user;
        $name = $user->name;

        // Safety check: only unverified members can be rejected/discarded
        if ($member->is_verified) {
            return back()->with('error', "Gagal menolak. Member '{$name}' sudah berstatus terverifikasi.");
        }

        $member->delete();
        $user->delete();

        return back()->with('success', "Pendaftaran member '{$name}' berhasil ditolak dan akun telah dihapus.");
    }
}
