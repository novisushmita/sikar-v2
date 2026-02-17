<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua order yang butuh penugasan (status assigned, on-process, completed)
        $orders = DB::table('order')
            ->whereIn('status', ['assigned', 'on-process', 'completed'])
            ->get();

        // Ambil contoh ID sopir dan mobil yang ada di DB (asumsi sudah ada isinya)
        $sopirIds = DB::table('sopir')->pluck('sopir_id')->toArray();
        $mobilIds = DB::table('mobil')->pluck('mobil_id')->toArray();

        // Pastikan ada data sopir dan mobil agar tidak error
        if (empty($sopirIds) || empty($mobilIds)) {
            $this->command->warn("Data sopir atau mobil kosong. Seeder dibatalkan.");
            return;
        }

        $assignments = [];

        foreach ($orders as $order) {
            $assignments[] = [
                'order_id' => $order->order_id,
                'sopir_id' => $sopirIds[array_rand($sopirIds)], // Pilih sopir acak
                'mobil_id' => $mobilIds[array_rand($mobilIds)], // Pilih mobil acak
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ];
        }

        DB::table('order_assignment')->insert($assignments);
    }
}