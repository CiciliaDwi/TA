<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Nota_Jual;
use App\Models\Nota_Jual_Detil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bestSellers = Nota_Jual_Detil::select(
            'barang.KodeBarang',
            'barang.Nama as NamaBarang',
            DB::raw('SUM(nota_jual_detil.Jumlah) as TotalTerjual'),
            DB::raw('SUM(nota_jual_detil.Jumlah * nota_jual_detil.Harga) as TotalPendapatan')
        )
        ->join('barang', 'barang.KodeBarang', '=', 'nota_jual_detil.KodeBarang')
        ->join('nota_jual', 'nota_jual.NoNota', '=', 'nota_jual_detil.NoNota')
        ->whereMonth('nota_jual.tanggal', date('m'))
        ->whereYear('nota_jual.tanggal', date('Y'))
        ->groupBy('barang.KodeBarang', 'barang.Nama')
        ->orderBy('TotalTerjual', 'desc')
        ->take(10)
        ->get();

        // Stok menipis (< 10)
        $lowStock = Barang::with('kategori')
            ->where('Stok', '<', 10)
            ->orderBy('Stok', 'asc')
            ->get();

        // Semua penjualan
        $allSales = Nota_Jual::with(['pegawai', 'detil.barang'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('report.report', compact('bestSellers', 'lowStock', 'allSales'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
