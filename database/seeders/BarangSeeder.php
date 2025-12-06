<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = public_path('collection/product_collection.json');
        $jsonString = file_get_contents($jsonPath);
        $items = json_decode($jsonString, true);

        foreach ($items as $item) {
            $categoryCode = Kategori::query()->where('KodeKategori', $item['Kategori Produk'])->first()?->KodeKategori ?? Kategori::inRandomOrder()->first()->KodeKategori;
            Barang::create([
                'KodeBarang' => $item['Kode'],
                'Barcode' => fake()->numberBetween(1234567890000, 1234567890123),
                'Nama' => $item['Nama Produk'],
                'Merek' => $item['Merek'],
                'Satuan' => $item['Satuan'],
                'HargaJual' => $item['Omzet'] / $item['Qty Konversi'],
                'Stok' => fake()->numberBetween(10, 50),
                'KodeKategori' => $categoryCode,
            ]);
        }
    }
}
