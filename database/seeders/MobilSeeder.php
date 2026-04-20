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
            ['mobil_id' => 'Z 1588 KR', 'deskripsi' => 'Toyota Fortuner', 'kapasitas' => 8, 'availability' => 1],
            ['mobil_id' => 'Z 1589 KR', 'deskripsi' => 'Toyota Innova', 'kapasitas' => 8, 'availability' => 1],
            ['mobil_id' => 'Z 1590 KR', 'deskripsi' => 'Toyota Innova', 'kapasitas' => 8, 'availability' => 1],
            ['mobil_id' => 'Z 1591 KR', 'deskripsi' => 'Toyota Innova', 'kapasitas' => 8, 'availability' => 1],
            ['mobil_id' => 'Z 1421 KR', 'deskripsi' => 'Toyota Innova', 'kapasitas' => 8, 'availability' => 1],
            ['mobil_id' => 'Z 1422 KR', 'deskripsi' => 'Toyota Innova', 'kapasitas' => 8, 'availability' => 1],
            ['mobil_id' => 'Z 1423 KR', 'deskripsi' => 'Toyota Innova', 'kapasitas' => 8, 'availability' => 1],
            ['mobil_id' => 'Z 7135 KR', 'deskripsi' => 'Toyota Hiace', 'kapasitas' => 16, 'availability' => 1],
            ['mobil_id' => 'Z 8133 KR', 'deskripsi' => 'Toyota Hilux Patroli Cabin', 'kapasitas' => 5, 'availability' => 1],
            ['mobil_id' => 'Z 8134 KR', 'deskripsi' => 'Toyota Hilux Cluster', 'kapasitas' => 5, 'availability' => 1],
            ['mobil_id' => 'Z 8135 KR', 'deskripsi' => 'Toyota Hilux Single Cabin', 'kapasitas' => 2, 'availability' => 1],
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