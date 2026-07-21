<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Borrow;
use App\Models\MemberResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Tampilkan daftar member, peminjaman, dan reset password yang butuh verifikasi.
     */
    public function index()
    {
        $pendingMembers = Member::where('status', 'pending')->with('user')->get();
        
        $pendingBorrows = Borrow::where('status', 'pending')->with(['member.user', 'book'])->get();

        $pendingResets = MemberResetRequest::where('status', 'pending')->with('user')->get();
        
        return view('dashboards.petugas_verification', compact('pendingMembers', 'pendingBorrows', 'pendingResets'));
    }

    /**
     * Verifikasi/approve member.
     */
    public function approveMember(Member $member)
    {
        $member->update(['status' => 'active']);
        return back()->with('success', "Member {$member->user->name} berhasil diverifikasi.");
    }
    
    /**
     * Tolak member (hapus atau update status).
     */
    public function rejectMember(Member $member)
    {
        $user = $member->user;
        $name = $user ? $user->name : 'Member';
        
        $member->delete();
        if ($user) {
            $user->delete();
        }
        
        return back()->with('success', "Pendaftaran member {$name} ditolak dan akunnya telah dihapus dari sistem.");
    }

    /**
     * Verifikasi/approve peminjaman online.
     */
    public function approveBorrow(Borrow $borrow)
    {
        // Pastikan stok masih ada
        if ($borrow->book->available_stock <= 0) {
            return back()->with('error', "Stok buku '{$borrow->book->title}' sedang habis, peminjaman tidak bisa disetujui.");
        }
        
        $borrow->update(['status' => 'borrowed']);
        
        // Kurangi stok buku saat disetujui
        $borrow->book->decrement('available_stock');
        $borrow->book->update(['is_available' => $borrow->book->available_stock > 0]);

        // Tambah total_loans member saat disetujui
        $borrow->member->increment('total_loans');
        
        return back()->with('success', "Peminjaman buku '{$borrow->book->title}' oleh '{$borrow->member->user->name}' disetujui.");
    }
    
    /**
     * Tolak peminjaman online.
     */
    public function rejectBorrow(Borrow $borrow)
    {
        $borrow->update(['status' => 'rejected']);
        return back()->with('success', "Permintaan peminjaman buku ditolak.");
    }

    /**
     * Setujui permintaan reset password member.
     */
    public function approveResetRequest(MemberResetRequest $resetRequest)
    {
        $token = Str::random(60);
        
        $resetRequest->update([
            'status' => 'approved',
            'token' => $token,
        ]);

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $resetRequest->user->email]);

        // Log the link
        Log::info("Reset Password Link for Member ({$resetRequest->user->email}): {$resetUrl}");

        return back()->with('success', "Permintaan reset password untuk {$resetRequest->user->name} disetujui. Link reset: {$resetUrl}")
                     ->with('simulated_link', $resetUrl);
    }

    /**
     * Tolak permintaan reset password member.
     */
    public function rejectResetRequest(MemberResetRequest $resetRequest)
    {
        $name = $resetRequest->user->name;
        $resetRequest->delete();

        return back()->with('success', "Permintaan reset password untuk {$name} ditolak.");
    }
}
