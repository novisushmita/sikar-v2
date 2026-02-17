<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoryBekerjaSopirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua sopir_id yang ada
        $sopirIds = DB::table('sopir')->pluck('sopir_id')->toArray();

        if (empty($sopirIds)) {
            $this->command->warn('Tidak ada data sopir. Jalankan SopirSeeder terlebih dahulu.');
            return;
        }

        $historyBekerja = [];
        $now = Carbon::now();

        // Generate data untuk 3 bulan terakhir
        foreach ($sopirIds as $sopirId) {
            // Random berapa hari sopir bekerja (antara 50-80 hari dari 90 hari)
            $jumlahHariBekerja = rand(50, 80);
            
            for ($i = 0; $i < $jumlahHariBekerja; $i++) {
                // Random tanggal dalam 90 hari terakhir
                $tanggal = $now->copy()->subDays(rand(0, 90))->format('Y-m-d');
                
                // Cek apakah kombinasi sopir_id dan tanggal sudah ada
                $key = $sopirId . '_' . $tanggal;
                
                if (!isset($historyBekerja[$key])) {
                    $historyBekerja[$key] = [
                        'tanggal' => $tanggal,
                        'order_completed' => rand(1, 15), // Random 1-15 order per hari
                        'sopir_id' => $sopirId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Insert data dalam batch untuk performa lebih baik
        $chunks = array_chunk(array_values($historyBekerja), 500);
        
        foreach ($chunks as $chunk) {
            DB::table('history_bekerja_sopir')->insert($chunk);
        }

        $this->command->info('History bekerja sopir berhasil di-seed dengan ' . count($historyBekerja) . ' records.');
    }
}