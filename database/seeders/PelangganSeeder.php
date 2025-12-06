<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            Pelanggan::create([
                'KodePelanggan' => 100 + $i,
                'Nama' => fake('ID_id')->name,
                'Alamat' => fake('ID_id')->address,
                'Telepon' => fake('ID_id')->phoneNumber,
            ]);
        }
    }
}
