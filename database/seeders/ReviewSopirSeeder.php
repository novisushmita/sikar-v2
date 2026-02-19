<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSopirSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $data = DB::table('order as o')
            ->join('order_assignment as oa', 'o.order_id', '=', 'oa.order_id')
            ->select(
                'o.order_id',
                'o.pengguna_id',
                'oa.sopir_id'
            )
            ->get();

        $dataReview = [];

        foreach ($data as $row) {

            // Cegah duplicate review
            $exists = DB::table('review_sopir')
                ->where('order_id', $row->order_id)
                ->exists();

            if ($exists) {
                continue;
            }

            $dataReview[] = [
                'tanggal'     => $now,
                'review'      => rand(3, 5),
                'sopir_id'    => $row->sopir_id,
                'pengguna_id' => $row->pengguna_id,
                'order_id'    => $row->order_id,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        DB::table('review_sopir')->insert($dataReview);
    }
}
