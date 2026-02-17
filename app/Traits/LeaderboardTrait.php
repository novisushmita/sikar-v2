<?php

namespace App\Traits;

use App\Models\Sopir;

trait LeaderboardTrait
{
    public function getLeaderboard()
    {
        $allData = Sopir::orderBy('order_completed', 'desc')
        ->orderBy('updated_at' ,'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data leaderboard sopir berhasil diambil',
            'count' => $allData->count(),
            'data' => $allData
        ]);
    }
}