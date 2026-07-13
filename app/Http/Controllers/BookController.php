<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
        return view('books.create');
    }

    /**
     * Store a newly created book in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|unique:books,barcode',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'year' => 'required|integer|min:1000|max:' . date('Y'),
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512',
        ], [
            'barcode.unique' => 'Barcode ini sudah terdaftar pada buku lain.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'year.max' => 'Tahun terbit tidak boleh melebihi tahun saat ini.',
<<<<<<< HEAD
            'cover_image.max' => 'Ukuran gambar sampul tidak boleh lebih dari 512 KB.',
=======
            'cover_image.max' => 'Ukuran gambar cover tidak boleh melebihi 512 KB.'
>>>>>>> origin/pr-1
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
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified book in database.
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'barcode' => 'required|string|unique:books,barcode,' . $book->id,
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'year' => 'required|integer|min:1000|max:' . date('Y'),
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512',
        ], [
<<<<<<< HEAD
            'cover_image.max' => 'Ukuran gambar sampul tidak boleh lebih dari 512 KB.',
=======
            'barcode.unique' => 'Barcode ini sudah terdaftar pada buku lain.',
            'year.integer' => 'Tahun terbit harus berupa angka.',
            'year.max' => 'Tahun terbit tidak boleh melebihi tahun saat ini.',
            'cover_image.max' => 'Ukuran gambar cover tidak boleh melebihi 512 KB.'
>>>>>>> origin/pr-1
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
}
