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

        // Limit: Maksimal 5 pengajuan reset password per hari (24 jam)
        $todayResetCount = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        $todayMemberReqCount = MemberResetRequest::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        if (($todayResetCount + $todayMemberReqCount) >= 5) {
            return back()->with('error', 'Anda telah mencapai batas maksimal pengajuan reset password hari ini. Silakan coba kembali besok.');
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

        // Limit check
        $todayReqCount = MemberResetRequest::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        if ($todayReqCount >= 5) {
            return redirect()->route('password.request')->with('error', 'Anda telah mencapai batas maksimal pengajuan reset password hari ini. Silakan coba kembali besok.');
        }

        $inputAnswer = strtolower(trim($request->security_answer ?? ''));
        $actualAnswer = strtolower(trim($user->security_answer ?? ''));

        // Jika user memilih untuk langsung kirim pengajuan ke Admin (misal lupa jawaban)
        if ($request->has('request_admin')) {
            MemberResetRequest::create([
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

            return redirect()->route('login')->with('success', 'Pengajuan reset password telah dikirimkan ke Admin. Silakan tunggu verifikasi/persetujuan dari Admin atau Petugas.');
        }

        if ($inputAnswer !== $actualAnswer) {
            return back()->with('error', 'Jawaban pertanyaan keamanan salah. Silakan coba lagi atau kirim pengajuan bantuan ke Admin.')
                         ->with('show_admin_option', true);
        }

        // Simpan pengajuan reset password dengan status 'approved' dan generate token
        $token = Str::random(60);

        MemberResetRequest::create([
            'user_id' => $user->id,
            'status' => 'approved',
            'token' => $token,
        ]);

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Log link untuk referensi / kirim email
        Log::info("Reset Password Link for Member ({$user->email}): {$resetUrl}");

        return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email])
                         ->with('success', 'Jawaban benar! Silakan atur password baru Anda.')
                         ->with('simulated_link', $resetUrl);
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
