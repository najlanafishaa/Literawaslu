<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BookDeletionRequest;
use Illuminate\Http\Request;

class SuperAdminActivityController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function activities()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('superadmin.activities', compact('logs'));
    }

    /**
     * Display a listing of book deletion requests.
     */
    public function deletionRequests()
    {
        $requests = BookDeletionRequest::with(['book', 'requestedBy'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        return view('superadmin.deletion_requests', compact('requests'));
    }

    /**
     * Approve a book deletion request.
     */
    public function approveDeletion(Request $request, $id)
    {
        $delReq = BookDeletionRequest::findOrFail($id);
        
        if ($delReq->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $book = $delReq->book;
        
        if ($book) {
            if (!$book->is_available) {
                return back()->with('error', 'Gagal menyetujui penghapusan. Buku sedang dipinjam.');
            }
            
            $title = $book->title;
            $book->delete(); // This will cascade delete or we might need to handle it

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Menyetujui Hapus Buku',
                'description' => "Menyetujui penghapusan buku: {$title} yang diajukan oleh " . $delReq->requestedBy->name,
                'ip_address' => $request->ip()
            ]);
        }

        $delReq->update(['status' => 'approved']);

        return back()->with('success', 'Penghapusan buku berhasil disetujui.');
    }

    /**
     * Reject a book deletion request.
     */
    public function rejectDeletion(Request $request, $id)
    {
        $delReq = BookDeletionRequest::findOrFail($id);
        
        if ($delReq->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $delReq->update(['status' => 'rejected']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Menolak Hapus Buku',
            'description' => "Menolak penghapusan buku: " . ($delReq->book ? $delReq->book->title : 'Buku tidak ditemukan'),
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Penghapusan buku ditolak.');
    }
}
