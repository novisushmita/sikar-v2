<?php

namespace App\Traits;

use App\Models\Sopir;
use Exception;

trait DataSopirTrait
{
    public function getSopir()
    {
        try {
            $sopir = Sopir::available()
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Data sopir tersedia berhasil diambil',
                'count' => $sopir->count(),
                'data' => $sopir
            ]);

        } catch (Exception $e) {
             return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data sopir tersedia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}