<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('order')->insert([
            // ===== ORDER UNTUK NOVI ID 18 =====
            [
                'pengguna_id' => 18,
                'tempat_penjemputan' => 'Stasiun Solo Balapan',
                'tempat_tujuan' => 'Mall Solo Paragon',
                'waktu_penjemputan' => Carbon::now()->subHours(2),
                'keterangan' => 'Dinas',
                'status' => 'completed',
                'created_at' => $now->copy()->subHours(3),
                'updated_at' => $now->copy()->subHours(1),
            ],
            [
                'pengguna_id' => 18,
                'tempat_penjemputan' => 'Terminal Tirtonadi',
                'tempat_tujuan' => 'Universitas Sebelas Maret',
                'waktu_penjemputan' => Carbon::now()->addHours(1),
                'keterangan' => 'Dinas',
                'status' => 'on-process',
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->addHours(1),
            ],
            [
                'pengguna_id' => 18,
                'tempat_penjemputan' => 'Mall Solo Paragon',
                'tempat_tujuan' => 'Stasiun Solo Balapan',
                'waktu_penjemputan' => Carbon::now()->subDays(3),
                'keterangan' => '-',
                'status' => 'completed',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3)->addHours(2),
            ],

            // ===== ORDER UNTUK SAIRA ID 19 =====
            [
                'pengguna_id' => 19,
                'tempat_penjemputan' => 'Alun-alun Kidul Solo',
                'tempat_tujuan' => 'Keraton Kasunanan Solo',
                'waktu_penjemputan' => Carbon::now()->subMinutes(30),
                'keterangan' => 'Dinas',
                'status' => 'completed',
                'created_at' => $now->copy()->subHours(1),
                'updated_at' => $now,
            ],
            [
                'pengguna_id' => 19,
                'tempat_penjemputan' => 'Solo Grand Mall',
                'tempat_tujuan' => 'Pasar Klewer',
                'waktu_penjemputan' => Carbon::now()->subMinutes(15),
                'keterangan' => 'Dinas',
                'status' => 'completed',
                'created_at' => $now->copy()->subMinutes(30),
                'updated_at' => $now->copy()->subMinutes(5),
            ],
            [
                'pengguna_id' => 19,
                'tempat_penjemputan' => 'Pasar Gede',
                'tempat_tujuan' => 'Rumah Sakit',
                'waktu_penjemputan' => Carbon::now()->subHours(5),
                'keterangan' => 'Dinas',
                'status' => 'completed',
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(5),
            ],
            [
                'pengguna_id' => 19,
                'tempat_penjemputan' => 'Taman Sriwedari',
                'tempat_tujuan' => 'Bandara Adi Soemarmo',
                'waktu_penjemputan' => Carbon::now()->addDays(1),
                'keterangan' => '-',
                'status' => 'assigned',
                'created_at' => $now,
                'updated_at' => $now->copy()->subMinutes(10),
            ],
        ]);
    }
}