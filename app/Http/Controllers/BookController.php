<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
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
        $defaultCategories = [
            'pemerintahan', 'november', 'hukum dan undang-undang', 'motivasi', 
            'politik', 'sosial', 'demokrasi', 'keagamaan', 'sengketa pemilu', 
            'riset pilkada', 'akuntansi', 'skripsi', 'laporan hasil pengawasan'
        ];
        $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        $categories = array_unique(array_merge($defaultCategories, $dbCategories));
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
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:1',
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
        $data['available_stock'] = $request->stock;
        $data['is_available'] = $request->stock > 0;

        if ($request->hasFile('cover_image')) {
            $imageName = time() . '.' . $request->cover_image->extension();
            $request->cover_image->move(public_path('images/covers'), $imageName);
            $data['cover_image'] = 'images/covers/' . $imageName;
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Buku baru berhasil ditambahkan ke dalam sistem.');
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        $defaultCategories = [
            'pemerintahan', 'november', 'hukum dan undang-undang', 'motivasi', 
            'politik', 'sosial', 'demokrasi', 'keagamaan', 'sengketa pemilu', 
            'riset pilkada', 'akuntansi', 'skripsi', 'laporan hasil pengawasan'
        ];
        $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        $categories = array_unique(array_merge($defaultCategories, $dbCategories));
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
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:1',
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

        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Remove the specified book from database.
     */
    public function destroy(Book $book)
    {
        // Check if book is currently borrowed
        if (!$book->is_available) {
            return back()->with('error', 'Gagal menghapus buku. Buku sedang dalam status dipinjam.');
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus dari sistem.');
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
        if ($user->role === 'member') {
            $memberId = $user->member->id;

            // Cek apakah masih pending (belum diverifikasi)
            $hasPendingBorrow = \App\Models\Borrow::where('member_id', $memberId)
                ->where('book_id', $book->id)
                ->where('status', 'pending')
                ->exists();
            if ($hasPendingBorrow) {
                return back()->with('error', 'Permintaan peminjaman Anda untuk buku ini sedang menunggu verifikasi Admin. Baca online baru bisa dilakukan setelah disetujui.');
            }

            $hasActiveBorrow = \App\Models\Borrow::where('member_id', $memberId)
                ->where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->exists();
                
            if (!$hasActiveBorrow) {
                return back()->with('error', 'Anda harus meminjam dan mendapat persetujuan Admin terlebih dahulu untuk membaca buku ini secara online.');
            }
        }

        // Convert Google Drive link to embeddable preview URL
        $embedUrl = $this->convertToEmbedUrl($book->drive_link);

        return view('books.read', compact('book', 'embedUrl'));
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
