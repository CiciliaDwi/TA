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
        $jsonPath = public_path('collection/category_collection.json');
        $jsonString = file_get_contents($jsonPath);
        $categories = json_decode($jsonString, true);

        foreach ($categories as $i => $value) {
            Kategori::create([
                'KodeKategori' => "K-{$i}",
                'Nama' => $value['Category Name'],
            ]);
        }
    }
}
