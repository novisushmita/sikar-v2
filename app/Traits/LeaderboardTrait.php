<?php

namespace App\Traits;

use App\Models\Sopir;
use App\Models\ReviewSopir;

use Carbon\Carbon;

trait LeaderboardTrait
{
    public function getLeaderboard()
    {
        $allData = Sopir::withCount(['review as review' => function ($query) {
                $query->where('tanggal', Carbon::today());
        }])
        ->orderBy('order_completed', 'desc')
        ->orderBy('updated_at', 'asc')
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data leaderboard sopir berhasil diambil',
            'count' => $allData->count(),
            'data' => $allData
        ]);
    }
}