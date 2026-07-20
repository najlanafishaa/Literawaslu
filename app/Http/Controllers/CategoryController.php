<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori ini sudah ada.',
        ]);

        Category::create(['name' => $request->name]);

        return back()->with('success', "Kategori '{$request->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori ini sudah ada.',
        ]);

        $old = $category->name;
        $category->update(['name' => $request->name]);

        return back()->with('success', "Kategori '{$old}' berhasil diubah menjadi '{$category->name}'.");
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', "Kategori '{$category->name}' berhasil dihapus.");
    }
}
