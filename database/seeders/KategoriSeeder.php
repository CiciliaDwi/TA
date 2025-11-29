<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'ACC',
            'ANTI GORES HYDROGEL',
            'Antigores Biasa',
            'Antigores full',
            'BATOK CHARGER',
            'CHARGER MOBIL',
            'HEADSET',
            'HOLDER MOBIL',
            'KABEL DATA',
            'KARTU PERDANA',
            'KARTU PERDANA INTERNET',
            'KARTU PERDANA/ perdana',
            'KARTU PERDANA/perdana',
            'MEMORI EKSTERNAL/memori card',
            'POWERBANK',
            'RING STAND',
        ];

        foreach ($categories as $i => $value) {
            Kategori::create([
                'KodeKategori' => "C{$i}",
                'Nama' => $value,
            ]);
        }
    }
}
