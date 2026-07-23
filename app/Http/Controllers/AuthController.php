<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Check member status if role is member
            if ($user->role === 'member' && $user->member) {
                if ($user->member->status === 'pending') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->with('warning', 'Akun Anda sedang menunggu verifikasi dari Admin. Silakan hubungi petugas perpustakaan.');
                }
                
                if ($user->member->status === 'rejected') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->with('error', 'Pendaftaran akun Anda ditolak oleh Admin.');
                }
            }
            
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', "Selamat datang kembali, {$user->name}!");
        }

        $userExists = User::where('email', $request->email)->exists();
        if (!$userExists) {
            return back()->withErrors([
                'email' => 'Anda belum punya akun, silakan daftar terlebih dahulu.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'security_question' => 'required|string|max:255',
            'security_answer' => 'required|string|max:255',
        ], [
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'security_question.required' => 'Pertanyaan keamanan wajib dipilih.',
            'security_answer.required' => 'Jawaban keamanan wajib diisi.',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'member', // default role
                'security_question' => $request->security_question,
                'security_answer' => strtolower(trim($request->security_answer)), // lowercase and trim for easier comparison
            ]);

            // Generate sequential member code starting from MEM-0000001
            $lastMember = Member::orderBy('id', 'desc')->first();
            $nextNum = $lastMember ? ((int) str_replace('MEM-', '', $lastMember->member_code)) + 1 : 1;
            $code = 'MEM-' . str_pad($nextNum, 7, '0', STR_PAD_LEFT);

            // Create Member Profile
            // Set status to pending so it must be verified by admin
            $member = Member::create([
                'user_id' => $user->id,
                'member_code' => $code,
                'total_loans' => 0,
                'points' => 10,
                'borrow_limit' => 1,
                'status' => 'pending',
            ]);

            // Record initial bonus point history
            \App\Models\PointHistory::create([
                'member_id' => $member->id,
                'type' => 'earn',
                'points' => 10,
                'description' => 'Bonus Poin Registrasi Akun Baru',
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('unverified');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->withErrors(['email' => 'Terjadi kesalahan saat menyimpan data pendaftaran. Silakan coba lagi.']);
        }
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Show profile edit page.
     */
    public function showProfile()
    {
        return view('auth.profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Handle profile update.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        // Only validate password if the user filled it
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $validated = $request->validate($rules, [
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.'
        ]);

        // Update fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Show unverified account landing page.
     */
    public function showUnverified()
    {
        return view('auth.unverified');
    }
}
