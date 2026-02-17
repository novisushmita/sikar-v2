<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MobilSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $daftarMobil = [
            [
                'mobil_id' => 'Z 1234 AB',
                'deskripsi' => 'Toyota Avanza - Putih (2020)',
                'kapasitas' => 7,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 5678 CD',
                'deskripsi' => 'Toyota Avanza - Hitam (2021)',
                'kapasitas' => 7,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 9012 EF',
                'deskripsi' => 'Toyota Innova Reborn - Silver (2022)',
                'kapasitas' => 8,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 3456 GH',
                'deskripsi' => 'Toyota Innova Reborn - Hitam (2021)',
                'kapasitas' => 8,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 7890 IJ',
                'deskripsi' => 'Toyota Hiace - Putih (2019)',
                'kapasitas' => 16,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 1122 KL',
                'deskripsi' => 'Honda CR-V - Abu-abu (2020)',
                'kapasitas' => 5,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 3344 MN',
                'deskripsi' => 'Mitsubishi Pajero Sport - Hitam (2022)',
                'kapasitas' => 7,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 5566 OP',
                'deskripsi' => 'Suzuki Ertiga - Merah (2021)',
                'kapasitas' => 7,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 7788 QR',
                'deskripsi' => 'Daihatsu Xenia - Biru (2020)',
                'kapasitas' => 7,
                'availability' => 1,
            ],
            [
                'mobil_id' => 'Z 9900 ST',
                'deskripsi' => 'Toyota Fortuner - Putih (2023)',
                'kapasitas' => 7,
                'availability' => 1,
            ],
        ];

        foreach ($daftarMobil as $mobil) {
            DB::table('mobil')->insert([
                'mobil_id' => $mobil['mobil_id'],
                'deskripsi' => $mobil['deskripsi'],
                'kapasitas' => $mobil['kapasitas'],
                'availability' => $mobil['availability'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}