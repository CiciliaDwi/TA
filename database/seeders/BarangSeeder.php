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
        $items = [
            'AG BENING 0,8 PREMIUM VANVO',
            'AG BENING FULL PREMIUM VANVO',
            'AG BENING GLASS CURVE UV/LENGKUNG PRO',
            'AG BIASA PRO',
            'AG FULL ESD (JEMPOL)',
            'AG FULL ESD DIAMOND',
            'AG GEL ROBOT GLEAR',
            'AG GEl logo pisau',
            'AG LENGKUNG NON UV',
            'AG PRIVASI CERAMIC GLASS PRO',
            'AG PRIVASI CERAMIC MATTE PRO',
            'AG PRIVASI GLASS KING KONG',
            'AIRPODS PRO TWS',
            'BATOK CAS 100W (REALME, OPPO, VIVO, INFINIX)',
            'BATOK CAS C TO C UNIVERSAL CM24',
            'BATOK CHARGER 20W PORT C WELLCOMM',
            'BATOK CHARGER LO-C56/SPEEDY V PD 20W LOGON',
            'BATOK CHARGER ROBOT RT-K10i',
            'BATOK CHARGER ROBOT RT-K8',
            'BATOK CHARGER ROBOT RT-K9',
            'BATOK CHARGER VIVAN',
            'BATOK CAS VIVAN 30W',
            'BATOK ROBOT 1 USB 1A RT-K4S',
            'BATOK ROBOT RT-K1',
            'BATOK ROBOT RT-K2',
            'BATOK ROBOT RT-K5S',
            'BLUETOOTH VIVAN',
            'CAR CHARGER DUAL USB 2.4A LO-SV20 LOGON',
            'CAR CHARGER HK-K09 2,4A',
            'CAR CHARGER ROBOT RT-C06',
            'CAR CHARGER ROBOT RT-C09',
            'CAR HOLDER ROBOT RT-CH12',
            'CAR HOLDER ROBOT RT-CH23',
            'CAR HOLDER ROBOT RT-CH30',
            'CAR HOLDER ROBOT RT-CH31',
            'CAR HOLDER ROBOT RT-CH32',
            'CAR HOLDER ROBOT RT-CH33',
            'CAR HOLDER ROBOT RT-CH34',
            'CAR HOLDER VANVO',
            'CASE MOTIF COW/CEW TIPE CAMPUR',
            'CASE ROBOT',
            'HOLDER ROBOT RT-CH06',
            'HEADSET BLUETOOTH ROBOT RBT510',
            'HEADSET OPPO/VIVO ORI 99',
            'HEADSET ROBOT RE701',
            'HEADSET ROBOT RE702',
            'HEADSET ROBOT RE703',
            'HEADSET ROBOT RE704',
            'HEADSET ROBOT RE705',
            'HEADSET ROBOT RE707',
            'HEADSET ROBOT RE708',
            'HEADSET ROBOT RE710',
            'HEADSET ROBOT RE711',
            'HEADSET ROBOT RE712',
            'HEADSET ROBOT RE713',
        ];

        foreach ($items as $i => $value) {
            Barang::create([
                'KodeBarang' => "BR-{$i}",
                'Barcode' => fake()->numberBetween(1234567890000, 1234567890123),
                'Nama' => $value,
                'HargaJual' => fake()->numberBetween(10000, 50000),
                'Stok' => fake()->numberBetween(10, 50),
                'KodeKategori' => Kategori::inRandomOrder()->first()->KodeKategori,
            ]);
        }
    }
}
