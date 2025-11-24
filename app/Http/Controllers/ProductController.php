<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Barang::with('kategori')->get();
        return view('produk.listproduk', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Kategori::all();
        return view('produk.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:5|unique:barang,KodeBarang',
            'barcode' => 'required|string|digits_between:1,13|numeric|unique:barang,Barcode',
            'nama' => 'required|string|max:255',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategori' => 'required|exists:kategori,KodeKategori'
        ], [
            'kode_barang.required' => 'Kode barang harus diisi',
            'kode_barang.unique' => 'Kode barang sudah digunakan',
            'kode_barang.max' => 'Kode barang maksimal 5 karakter',
            'barcode.required' => 'Barcode harus diisi',
            'barcode.digits_between' => 'Barcode harus berupa 1-13 digit angka',
            'barcode.numeric' => 'Barcode harus berupa angka',
            'barcode.unique' => 'Barcode sudah digunakan',
            'nama.required' => 'Nama produk harus diisi',
            'harga_jual.required' => 'Harga jual harus diisi',
            'harga_jual.numeric' => 'Harga jual harus berupa angka',
            'harga_jual.min' => 'Harga jual tidak boleh negatif',
            'stok.required' => 'Stok harus diisi',
            'stok.integer' => 'Stok harus berupa angka bulat',
            'stok.min' => 'Stok tidak boleh negatif',
            'kategori.required' => 'Kategori harus dipilih',
            'kategori.exists' => 'Kategori tidak valid'
        ]);

        $product = new Barang();
        $product->KodeBarang = $request->kode_barang;
        $product->Barcode = $request->barcode;
        $product->Nama = $request->nama;
        $product->HargaJual = $request->harga_jual;
        $product->Stok = $request->stok;
        $product->KodeKategori = $request->kategori;
        $product->save();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
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
        $product = Barang::findOrFail($id);
        $categories = Kategori::all();
        return view('produk.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'barcode' => 'required|string|digits_between:1,13|numeric|unique:barang,Barcode,' . $id . ',KodeBarang',
            'nama' => 'required|string|max:255',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategori' => 'required|exists:kategori,KodeKategori'
        ], [
            'barcode.required' => 'Barcode harus diisi',
            'barcode.digits_between' => 'Barcode harus berupa 1-13 digit angka',
            'barcode.numeric' => 'Barcode harus berupa angka',
            'barcode.unique' => 'Barcode sudah digunakan',
            'nama.required' => 'Nama produk harus diisi',
            'harga_jual.required' => 'Harga jual harus diisi',
            'harga_jual.numeric' => 'Harga jual harus berupa angka',
            'harga_jual.min' => 'Harga jual tidak boleh negatif',
            'stok.required' => 'Stok harus diisi',
            'stok.integer' => 'Stok harus berupa angka bulat',
            'stok.min' => 'Stok tidak boleh negatif',
            'kategori.required' => 'Kategori harus dipilih',
            'kategori.exists' => 'Kategori tidak valid'
        ]);

        $product = Barang::findOrFail($id);
        $product->Barcode = $request->barcode;
        $product->Nama = $request->nama;
        $product->HargaJual = $request->harga_jual;
        $product->Stok = $request->stok;
        $product->KodeKategori = $request->kategori;
        $product->save();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Barang::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
