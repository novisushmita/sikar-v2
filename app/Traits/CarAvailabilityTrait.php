<?php

namespace App\Traits;

use App\Models\Mobil;

trait CarAvailabilityTrait
{
    public function checkAvailCar()
    {
        $avail = Mobil::where('availability', 1)
            ->orderBy('deskripsi', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data mobil yang tersedia berhasil diambil',
            'count' => $avail->count(),
            'data' => $avail
        ]);
    }
}