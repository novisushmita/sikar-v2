<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSopirSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Ambil semua pengguna dengan role sopir
        $sopir = DB::table('pengguna')
            ->where('role', 'sopir')
            ->get();

        // Ambil semua pengguna dengan role penumpang
        $penumpang = DB::table('pengguna')
            ->where('role', 'penumpang')
            ->get();

        $dataReview = [];

        foreach ($sopir as $s) {
            for ($i = 0; $i < 3; $i++) {
                // Pilih penumpang secara acak
                $randomPenumpang = $penumpang->random();

                $dataReview[] = [
                    'tanggal'     => $now,
                    'review'      => rand(1, 5),
                    'sopir_id'    => $s->pengguna_id,
                    'pengguna_id' => $randomPenumpang->pengguna_id,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        DB::table('review_sopir')->insert($dataReview);
    }
}
