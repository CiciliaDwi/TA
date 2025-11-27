<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

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
