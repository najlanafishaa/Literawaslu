<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Mail\QuestionSubmittedMail;
use App\Mail\QuestionRepliedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Submit question from FAB modal (Public / All pages)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|min:10|max:3000',
        ], [
            'name.required'    => 'Nama wajib diisi.',
            'email.required'   => 'Alamat email wajib diisi.',
            'email.email'      => 'Format alamat email tidak valid.',
            'message.required' => 'Pesan pertanyaan wajib diisi.',
            'message.min'      => 'Pesan pertanyaan minimal 10 karakter.',
        ]);

        $question = Question::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'message' => $validated['message'],
            'status'  => 'pending',
        ]);

        // Send email to official Bawaslu email
        $officialEmail = env('OFFICIAL_BAWASLU_EMAIL', 'literawaslu@gmail.com');
        try {
            Mail::to($officialEmail)->send(new QuestionSubmittedMail($question));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email pertanyaan ke Bawaslu: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih. Pertanyaan Anda telah berhasil dikirim. Jawaban akan dikirim ke alamat email yang Anda daftarkan.'
        ]);
    }

    /**
     * Display list of questions in Super Admin Dashboard
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Question::with('replier')->latest();

        if ($status && in_array($status, ['pending', 'replied'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $questions = $query->paginate(15)->withQueryString();
        $pendingCount = Question::where('status', 'pending')->count();
        $repliedCount = Question::where('status', 'replied')->count();

        return view('dashboards.admin_questions', compact('questions', 'pendingCount', 'repliedCount', 'status', 'search'));
    }

    /**
     * Reply to a question by Super Admin
     */
    public function reply(Request $request, Question $question)
    {
        $validated = $request->validate([
            'reply' => 'required|string|min:5',
        ], [
            'reply.required' => 'Balasan wajib diisi.',
            'reply.min'      => 'Balasan minimal 5 karakter.',
        ]);

        $question->update([
            'reply'      => $validated['reply'],
            'status'     => 'replied',
            'replied_at' => now(),
            'replied_by' => auth()->id(),
        ]);

        // Send reply email to user's registered email
        try {
            Mail::to($question->email)->send(new QuestionRepliedMail($question));
            $mailSent = true;
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email balasan ke pengguna: ' . $e->getMessage());
            $mailSent = false;
        }

        $msg = 'Balasan berhasil disimpan dan ' . ($mailSent ? 'telah dikirim ke email pengguna.' : 'namun gagal mengirim email (periksa konfigurasi mail).');

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Get count of pending questions for global badge
     */
    public function pendingCount()
    {
        return response()->json([
            'count' => Question::where('status', 'pending')->count()
        ]);
    }
}
