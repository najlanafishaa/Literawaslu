<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\ActivityLog;
use App\Models\BookDeletionRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
        }

        $books = $query->orderBy('created_at', 'desc')->get();
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        // Hanya ambil kategori dari tabel categories
        $categories = Category::orderBy('name')->pluck('name')->toArray();
        sort($categories);
        
        return view('books.create', compact('categories'));
    }

    /**
     * Store a newly created book in database.
     */
    public function store(Request $request)
    {
        if ($request->category === 'new_category_option') {
            $request->validate([
                'new_category' => 'required|string|max:100',
            ], [
                'new_category.required' => 'Nama kategori baru wajib diisi jika memilih opsi input manual.'
            ]);
            $newCat = trim($request->new_category);
            Category::firstOrCreate(['name' => $newCat]);
            $request->merge(['category' => $newCat]);
        }

        $request->validate([
            'barcode' => 'required|string|unique:books,barcode',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'year' => 'required|integer|min:1000|max:' . date('Y'),
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:512',
            'drive_link' => 'nullable|url|max:500',
        ], [
            'barcode.unique' => 'Barcode ini sudah terdaftar pada buku lain.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'year.max' => 'Tahun terbit tidak boleh melebihi tahun saat ini.',
            'cover_image.max' => 'Ukuran gambar sampul tidak boleh lebih dari 512 KB.',
            'drive_link.url' => 'Link Google Drive harus berupa URL yang valid.',
        ]);

        $data = $request->except('cover_image');
        $data['available_stock'] = $request->stock;
        $data['is_available'] = $request->stock > 0;

        if ($request->hasFile('cover_image')) {
            $imageName = time() . '.' . $request->cover_image->extension();
            $request->cover_image->move(public_path('images/covers'), $imageName);
            $data['cover_image'] = 'images/covers/' . $imageName;
        }

        $book = Book::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Menambah Buku',
            'description' => "Menambahkan buku baru dengan judul: {$book->title}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('books.index')->with('success', 'Buku baru berhasil ditambahkan ke dalam sistem.');
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        // Hanya ambil kategori dari tabel categories (sama seperti di method create)
        $categories = Category::orderBy('name')->pluck('name')->toArray();
        sort($categories);

        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book in database.
     */
    public function update(Request $request, Book $book)
    {
        if ($request->category === 'new_category_option') {
            $request->validate([
                'new_category' => 'required|string|max:100',
            ], [
                'new_category.required' => 'Nama kategori baru wajib diisi jika memilih opsi input manual.'
            ]);
            $newCat = trim($request->new_category);
            Category::firstOrCreate(['name' => $newCat]);
            $request->merge(['category' => $newCat]);
        }

        $request->validate([
            'barcode' => 'required|string|unique:books,barcode,' . $book->id,
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'year' => 'required|integer|min:1000|max:' . date('Y'),
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'stock' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512',
            'drive_link' => 'nullable|url|max:500',
        ], [
            'barcode.unique' => 'Barcode ini sudah terdaftar pada buku lain.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'year.max' => 'Tahun terbit tidak boleh melebihi tahun saat ini.',
            'cover_image.max' => 'Ukuran gambar sampul tidak boleh lebih dari 512 KB.',
            'drive_link.url' => 'Link Google Drive harus berupa URL yang valid.',
        ]);

        $data = $request->except('cover_image');
        
        // Calculate new available_stock based on updated stock limit
        $diff = $request->stock - $book->stock;
        $data['available_stock'] = $book->available_stock + $diff;
        if ($data['available_stock'] < 0) {
            $data['available_stock'] = 0;
        }
        $data['is_available'] = $data['available_stock'] > 0;

        if ($request->hasFile('cover_image')) {
            // Delete old cover image file if exists
            if ($book->cover_image && file_exists(public_path($book->cover_image))) {
                @unlink(public_path($book->cover_image));
            }
            $imageName = time() . '.' . $request->cover_image->extension();
            $request->cover_image->move(public_path('images/covers'), $imageName);
            $data['cover_image'] = 'images/covers/' . $imageName;
        }

        $book->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Mengubah Buku',
            'description' => "Memperbarui data buku: {$book->title}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Request $request, Book $book)
    {
        // Check if book is currently borrowed
        if (!$book->is_available) {
            return back()->with('error', 'Gagal menghapus buku. Buku sedang dalam status dipinjam.');
        }

        if (in_array(auth()->user()->role, ['admin', 'petugas'])) {
            BookDeletionRequest::create([
                'book_id' => $book->id,
                'requested_by' => auth()->id(),
                'status' => 'pending'
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Mengajukan Hapus Buku',
                'description' => "Mengajukan penghapusan buku: {$book->title}",
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('books.index')->with('success', 'Pengajuan penghapusan buku berhasil dikirim ke Super Admin.');
        }

        $title = $book->title;
        $book->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Menghapus Buku',
            'description' => "Menghapus buku: {$title}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus dari sistem.');
    }

    /**
     * Remove multiple books (Super Admin only)
     */
    public function bulkDelete(Request $request)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return back()->with('error', 'Anda tidak memiliki akses untuk aksi ini.');
        }

        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id'
        ]);

        $books = Book::whereIn('id', $request->book_ids)->get();
        $count = 0;

        foreach ($books as $book) {
            if ($book->is_available) {
                $title = $book->title;
                $book->delete();
                $count++;
                
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Menghapus Buku (Bulk)',
                    'description' => "Menghapus buku dari fitur bulk: {$title}",
                    'ip_address' => $request->ip()
                ]);
            }
        }

        return redirect()->route('books.index')->with('success', "Berhasil menghapus {$count} buku.");
    }

    /**
     * Show the book in preview/read-only mode via Google Drive embed.
     */
    public function read(Book $book)
    {
        if (!$book->drive_link) {
            return back()->with('error', 'Buku ini tidak memiliki link baca online.');
        }

        $user = auth()->user();
        $member = $user ? $user->member : null;

        if (!$member) {
            return back()->with('error', 'Anda harus terdaftar sebagai member untuk membaca online.');
        }

        // Check if member has an approved borrow request for this book
        $approvedBorrow = \App\Models\Borrow::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->first();

        if (!$approvedBorrow) {
            return back()->with('error', 'Akses Baca Online Ditolak! Peminjaman buku online harus melalui proses pengajuan & verifikasi persetujuan petugas terlebih dahulu.');
        }

        // Convert Google Drive link to embeddable preview URL
        $embedUrl = $this->convertToEmbedUrl($book->drive_link);

        return view('books.read', compact('book', 'embedUrl'));
    }

    /**
     * Show deleted books (Trash).
     */
    public function trash()
    {
        $books = Book::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('books.trash', compact('books'));
    }

    /**
     * Restore a deleted book.
     */
    public function restore($id)
    {
        $book = Book::onlyTrashed()->findOrFail($id);
        $book->restore();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Memulihkan Buku',
            'description' => "Memulihkan buku dari tempat sampah: {$book->title}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('books.trash')->with('success', 'Buku berhasil dipulihkan.');
    }

    /**
     * Permanently delete a book.
     */
    public function forceDelete($id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return back()->with('error', 'Hanya Admin yang dapat menghapus buku secara permanen.');
        }

        $book = Book::onlyTrashed()->findOrFail($id);
        $title = $book->title;
        $book->forceDelete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Hapus Permanen Buku',
            'description' => "Menghapus buku secara permanen: {$title}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('books.trash')->with('success', 'Buku berhasil dihapus secara permanen.');
    }

    /**
     * Convert a Google Drive URL to an embeddable preview URL.
     * Supports formats:
     *   - https://drive.google.com/file/d/FILE_ID/view
     *   - https://drive.google.com/open?id=FILE_ID
     */
    private function convertToEmbedUrl(string $url): string
    {
        // Extract file ID from various Google Drive URL formats
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        } elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        } else {
            // Fallback: use URL as-is
            return $url;
        }

        // Use preview mode (no download button)
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }
}
