<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OfficerController extends Controller
{
    /**
     * Display a listing of officers.
     */
    public function index()
    {
        $officers = User::whereIn('role', ['admin', 'super_admin'])->orderBy('created_at', 'desc')->get();
        return view('officers.index', compact('officers'));
    }

    /**
     * Show the form for creating a new officer.
     */
    public function create()
    {
        return view('officers.create');
    }

    /**
     * Store a newly created officer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,super_admin',
        ], [
            'email.unique' => 'Email ini sudah digunakan.'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('officers.index')->with('success', 'Akun admin/petugas baru berhasil didaftarkan.');
    }

    /**
     * Show the form for editing the specified officer.
     */
    public function edit(User $officer)
    {
        // Safety check
        if (!in_array($officer->role, ['admin', 'super_admin', 'petugas'])) {
            return redirect()->route('officers.index')->with('error', 'Akses ditolak. Pengguna bukan merupakan admin atau super admin.');
        }

        return view('officers.edit', compact('officer'));
    }

    /**
     * Update the specified officer.
     */
    public function update(Request $request, User $officer)
    {
        if (!in_array($officer->role, ['admin', 'super_admin', 'petugas'])) {
            return redirect()->route('officers.index')->with('error', 'Akses ditolak.');
        }

        // Prevent self-role adjustment
        if ($officer->id === auth()->id() && $request->role !== $officer->role) {
            return redirect()->route('officers.index')->with('error', 'Akses ditolak. Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $officer->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:admin,super_admin,user',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $oldRole = $officer->role;
        $officer->update($data);

        // If changed to member, create member profile
        if ($request->role === 'user' && $oldRole !== 'user') {
            // Delete old member profile if it already exists (unlikely, but safe)
            \App\Models\Member::where('user_id', $officer->id)->delete();

            do {
                $code = 'MEM-' . rand(100000, 999999);
            } while (\App\Models\Member::where('member_code', $code)->exists());

            \App\Models\Member::create([
                'user_id' => $officer->id,
                'member_code' => $code,
                'total_loans' => 0,
                'points' => 10,
                'borrow_limit' => 1,
                'is_verified' => true, // default verified when assigned by super admin
            ]);

            return redirect()->route('officers.index')->with('success', "Akun '{$officer->name}' berhasil diubah perannya menjadi User.");
        }

        return redirect()->route('officers.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * Remove the specified officer.
     */
    public function destroy(User $officer)
    {
        if (!in_array($officer->role, ['admin', 'super_admin', 'petugas'])) {
            return redirect()->route('officers.index')->with('error', 'Akses ditolak.');
        }

        // Prevent self-deletion
        if ($officer->id === auth()->id()) {
            return redirect()->route('officers.index')->with('error', 'Gagal menghapus. Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $officer->delete();

        return redirect()->route('officers.index')->with('success', 'Akun admin/petugas berhasil dihapus dari sistem.');
    }
}
