<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Kategori::all();

        return view('kategori.listkategori', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategori.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_kategori' => 'required|string|size:2|regex:/^[0-9]+$/|unique:kategori,KodeKategori',
            'nama' => 'required|string|max:255',
        ], [
            'kode_kategori.required' => 'Kode kategori harus diisi',
            'kode_kategori.size' => 'Kode kategori harus 2 digit',
            'kode_kategori.regex' => 'Kode kategori harus berupa angka',
            'kode_kategori.unique' => 'Kode kategori sudah digunakan',
            'nama.required' => 'Nama kategori harus diisi',
            'nama.max' => 'Nama kategori maksimal 255 karakter',
        ]);

        $kategori = new Kategori;
        $kategori->KodeKategori = $request->kode_kategori;
        $kategori->Nama = $request->nama;
        $kategori->save();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Kategori::findOrFail($id);

        return view('kategori.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $category = Kategori::findOrFail($id);
        $category->Nama = $request->nama;
        $category->save();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Kategori::findOrFail($id);

        // Cek apakah kategori memiliki produk terkait
        if ($category->barang()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk terkait');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
