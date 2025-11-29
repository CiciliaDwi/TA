<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            'nama' => 'Admin',
            'tglLahir' => now()->subYears(25),
            'alamat' => fake()->address,
            'gaji' => 5000000,
            'username' => 'admin',
            'password' => 'Sandi123',
            'jabatan' => 'admin',
        ]);

        User::query()->create([
            'nama' => 'Kasir',
            'tglLahir' => now()->subYears(25),
            'alamat' => fake()->address,
            'gaji' => 5000000,
            'username' => 'kasir',
            'password' => 'Sandi123',
            'jabatan' => 'kasir',
        ]);
    }
}
