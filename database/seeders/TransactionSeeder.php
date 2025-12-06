<?php

namespace Database\Seeders;

use App\Http\Controllers\TransactionController;
use App\Models\Barang;
use App\Models\Nota_Jual;
use App\Models\Nota_Jual_Detil;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $controller = new TransactionController;
            $NoNota = $controller->getLastNotaNumber()->getData(true)['nota_number'];

            $transaction = Nota_Jual::create([
                'NoNota' => $NoNota,
                'KodePelanggan' => Pelanggan::inRandomOrder()->first()->KodePelanggan,
                'Tanggal' => now(),
                'id_pegawai' => User::query()->where('jabatan', 'kasir')->first()->id,
                'metode_pembayaran' => fake()->randomElement(['cash', 'debit', 'kredit']),
            ]);

            $productCodesWithAmount = [
                Barang::inRandomOrder()->first()->KodeBarang => fake()->numberBetween(1, 5),
                Barang::inRandomOrder()->first()->KodeBarang => fake()->numberBetween(1, 5),
                Barang::inRandomOrder()->first()->KodeBarang => fake()->numberBetween(1, 5),
                Barang::inRandomOrder()->first()->KodeBarang => fake()->numberBetween(1, 5),
            ];

            $grandTotal = 0;
            foreach ($productCodesWithAmount as $key => $amount) {
                $product = Barang::find($key);
                $price = $product->HargaJual;
                $subtotal = $amount * $price;

                Nota_Jual_Detil::create([
                    'NoNota' => $transaction->NoNota,
                    'KodeBarang' => $product->KodeBarang,
                    'Jumlah' => $amount,
                    'Harga' => $price,
                    'Total' => $subtotal,
                ]);

                $grandTotal += $subtotal;
            }
        }
    }
}
