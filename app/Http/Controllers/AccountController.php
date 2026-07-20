<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->get();
        return view('dashboards.admin_accounts', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,petugas,member',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'member') {
            // Daftarkan sebagai member juga dengan status active (karena didaftarkan manual oleh admin)
            do {
                $code = 'MEM-' . rand(100000, 999999);
            } while (Member::where('member_code', $code)->exists());

            Member::create([
                'user_id' => $user->id,
                'member_code' => $code,
                'total_loans' => 0,
                'points' => 10,
                'borrow_limit' => 1,
                'status' => 'active', // Langsung active karena dari admin
            ]);
        }

        return back()->with('success', "Akun {$request->role} berhasil dibuat.");
    }

    public function demote(User $user)
    {
        if ($user->role !== 'super_admin') {
            return back()->with('error', 'Hanya super admin yang bisa di-demote.');
        }

        if (User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Tidak bisa men-demote satu-satunya super admin!');
        }

        $user->update(['role' => 'petugas']);
        
        return back()->with('success', "Akun {$user->name} berhasil diturunkan (demote) menjadi Admin Biasa (Petugas).");
    }
}
