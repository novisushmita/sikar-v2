<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SopirSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data pengguna yang memiliki role sopir
        $penggunaSopir = DB::table('pengguna')
            ->whereIn('role', ['sopir'])
            ->get();

        $dataSopir = [];

        foreach ($penggunaSopir as $p) {
            $dataSopir[] = [
                'sopir_id'        => $p->pengguna_id, // Menggunakan ID yang sama dari tabel pengguna sebagai primary key di tabel sopir
                'name'            => $p->name,
                'order_completed' => 0, // Data dummy
                'order_ongoing'   => 0,  // Data dummy
                'masuk_kerja'     => 0,           // Masuk kerja default aktif
                'created_at'      => $p->created_at,
                'updated_at'      => $p->updated_at,
            ];
        }

        // Masukkan ke tabel sopir
        DB::table('sopir')->insert($dataSopir);
    }
}