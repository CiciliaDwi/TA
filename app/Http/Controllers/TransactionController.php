<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Nota_Jual;
use App\Models\Nota_Jual_Detil;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $products = Barang::where('Stok', '>', 0)->get();
        $customers = Pelanggan::all();
        $paymentMethods = ['cash', 'debit', 'kredit'];

        return view('transaksi.transaction', compact('users', 'products', 'paymentMethods', 'customers'));
    }

    public function dashboard()
    {
        $recentTransactions = Nota_Jual::with(['pegawai', 'detil'])
            ->orderBy('Tanggal', 'desc')
            ->take(10)
            ->get();

        return view('home', compact('recentTransactions'));
    }

    public function getLastNotaNumber()
    {
        try {
            $prefix = 'NJ';

            $nextNumber = Nota_Jual::count() + 1;

            $year = now()->format('y');
            $month = now()->format('m');
            $day = now()->format('d');
            $sequence = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $notaNumber = $prefix.$year.$month.$day.$sequence; // ex: NJ250612003

            return response()->json([
                'nota_number' => $notaNumber,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi stok terlebih dahulu
            foreach ($request->products as $key => $product_id) {
                $quantity = $request->quantities[$key];
                $barang = Barang::find($product_id);

                if (! $barang) {
                    $message = 'Produk tidak ditemukan!';
                    session()->flash('error', $message);

                    return redirect()->back()->withInput($request->all());
                }

                if ($barang->Stok < $quantity) {
                    $message = "Stok `{$barang->Nama}` tidak mencukupi! Stok tersedia: {$barang->Stok}";
                    session()->flash('error', $message);

                    return redirect()->back()->withInput($request->all());
                }
            }

            $transaction = Nota_Jual::create([
                'NoNota' => $request->no_nota,
                'KodePelanggan' => $request->customer_code,
                'Tanggal' => now(),
                'id_pegawai' => $request->id_pegawai,
                'metode_pembayaran' => $request->payment_method,
            ]);

            $grandTotal = 0;

            foreach ($request->products as $key => $product_id) {
                $quantity = $request->quantities[$key];
                $price = $request->product_prices[$key];
                $subtotal = $quantity * $price;
                $grandTotal += $subtotal;

                Nota_Jual_Detil::create([
                    'NoNota' => $transaction->NoNota,
                    'KodeBarang' => $product_id,
                    'Jumlah' => $quantity,
                    'Harga' => $price,
                    'Total' => $subtotal,
                ]);

                $barang = Barang::lockForUpdate()->find($product_id);
                $barang->Stok -= $quantity;
                $barang->save();
            }

            $transaction->update(['total' => $grandTotal]);

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('transactions.index')
                ->with('error', 'Transaksi gagal: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Nota_Jual $nota_Jual)
    {
        $nota_Jual->load(['detil.barang', 'pegawai']);

        return view('transaksi.transactions.show', compact('nota_Jual'));
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
