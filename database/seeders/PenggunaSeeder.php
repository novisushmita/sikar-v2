<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $daftarPengguna = [
            ['name' => 'Asep Rohim', 'token' => 'K7M9Q2', 'role' => 'kepala_sopir', 'nomor' => '628278300001'],
            ['name' => 'Irpan',      'token' => '4XJ8PZ', 'role' => 'sopir',        'nomor' => '628278300002'],
            ['name' => 'Yoga',       'token' => 'M2Q9XA', 'role' => 'sopir',        'nomor' => '628278300003'],
            ['name' => 'Nandar',     'token' => '7PZ4KQ', 'role' => 'sopir',        'nomor' => '628278300004'],
            ['name' => 'Endang',     'token' => 'Q8X2M7', 'role' => 'sopir',        'nomor' => '628278300005'],
            ['name' => 'Dedi',       'token' => 'Z4M9P2', 'role' => 'sopir',        'nomor' => '628278300006'],
            ['name' => 'Budi',       'token' => '2QK7XM', 'role' => 'sopir',        'nomor' => '628278300007'],
            ['name' => 'Asep Rizal', 'token' => '9PXM4Q', 'role' => 'sopir',        'nomor' => '628278300008'],
            ['name' => 'Arya',       'token' => 'KQ7Z2M', 'role' => 'sopir',        'nomor' => '628278300009'],
            ['name' => 'Awan',       'token' => 'X92MPQ', 'role' => 'sopir',        'nomor' => '628278300010'],
            ['name' => 'Aang',       'token' => '4QZMP7', 'role' => 'sopir',        'nomor' => '628278300011'],
            ['name' => 'Taufik',     'token' => 'P7XQM2', 'role' => 'sopir',        'nomor' => '628278300012'],
            ['name' => 'Hamjah',     'token' => 'MZ92Q7', 'role' => 'sopir',        'nomor' => '628278300013'],
            ['name' => 'Yossi',      'token' => '7XQ4MP', 'role' => 'sopir',        'nomor' => '628278300014'],
            ['name' => 'Jajang',     'token' => 'QMPZ27', 'role' => 'sopir',        'nomor' => '628278300015'],
            ['name' => 'Anggi',      'token' => '2M7QPX', 'role' => 'sopir',        'nomor' => '628278300016'],
            ['name' => 'Uus',        'token' => 'ZQX927', 'role' => 'sopir',        'nomor' => '628278300017'],
            ['name' => 'Novi',       'token' => 'ZNX927', 'role' => 'penumpang',    'nomor' => '6281329645375'],
            ['name' => 'Saira',      'token' => 'ZSX727', 'role' => 'penumpang',    'nomor' => '6282295622004'],
        ];

        foreach ($daftarPengguna as $p) {
            DB::table('pengguna')->insert([
                'name'       => $p['name'],
                'token'      => $p['token'],
                'role'       => $p['role'],
                'nomor'      => $p['nomor'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}