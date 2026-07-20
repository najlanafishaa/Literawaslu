<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MemberResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form request reset password.
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses email untuk menentukan alur reset.
     */
    public function submitRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Alamat email tidak terdaftar di sistem.');
        }

        if ($user->role === 'member') {
            // Member -> Lanjut ke verifikasi pertanyaan keamanan
            return redirect()->route('password.security', ['email' => $user->email]);
        } else {
            // Admin & Super Admin -> Reset langsung via email
            $token = Str::random(60);

            // Simpan token ke password_reset_tokens
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

            // Kirim email (Simulasi dengan log Laravel dan session flash untuk demo)
            Log::info("Reset Password Link for Admin/Super Admin ({$user->email}): {$resetUrl}");

            return back()->with('success', 'Link reset password telah dikirim ke email Anda.')
                         ->with('simulated_link', $resetUrl);
        }
    }

    /**
     * Tampilkan form pertanyaan keamanan.
     */
    public function showSecurityQuestionForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role !== 'member') {
            return redirect()->route('password.request')->with('error', 'Akses tidak sah.');
        }

        return view('auth.security-question', compact('user'));
    }

    /**
     * Verifikasi jawaban keamanan dan kirim permintaan ke Admin.
     */
    public function verifySecurityQuestion(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'security_answer' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role !== 'member') {
            return redirect()->route('password.request')->with('error', 'Akses tidak sah.');
        }

        $inputAnswer = strtolower(trim($request->security_answer));
        $actualAnswer = strtolower(trim($user->security_answer));

        if ($inputAnswer !== $actualAnswer) {
            return back()->with('error', 'Jawaban pertanyaan keamanan salah. Silakan coba lagi.');
        }

        // Langsung generate token reset password tanpa persetujuan admin
        $token = Str::random(60);

        MemberResetRequest::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status' => 'approved',
                'token' => $token,
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Log link untuk referensi
        Log::info("Reset Password Link for Member ({$user->email}): {$resetUrl}");

        return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email])
                         ->with('success', 'Jawaban benar! Silakan atur password baru Anda.');
    }

    /**
     * Tampilkan form reset password baru.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;
        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Simpan password baru.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        if ($user->role === 'member') {
            // Untuk member, cek apakah ada permintaan reset yang disetujui
            $resetReq = MemberResetRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('token', $request->token)
                ->first();

            if (!$resetReq) {
                return redirect()->route('password.request')->with('error', 'Token reset password tidak valid atau kedaluwarsa.');
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            // Selesaikan permintaan
            $resetReq->delete();

            return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui. Silakan masuk menggunakan password baru.');
        } else {
            // Untuk Admin / Super Admin, cek token di password_reset_tokens
            $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if (!$record || !Hash::check($request->token, $record->token)) {
                return redirect()->route('password.request')->with('error', 'Token reset password tidak valid atau kedaluwarsa.');
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            // Hapus token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui. Silakan masuk menggunakan password baru.');
        }
    }
}
