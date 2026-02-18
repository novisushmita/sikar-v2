<?php

namespace App\Traits;

use App\Models\Sopir;
use App\Models\ReviewSopir;

use Carbon\Carbon;

trait LeaderboardTrait
{
    public function getLeaderboard()
    {
        $allData = Sopir::withAvg(['review as avg_review' => function ($query) {
            $query->whereDate('tanggal', Carbon::today());
        }], 'review')
        ->orderBy('order_completed', 'desc')
        ->orderBy('updated_at', 'asc')
        ->get()
        ->map(function ($item) {
            $item->avg_review = round($item->avg_review, 1);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Data leaderboard sopir berhasil diambil',
            'count' => $allData->count(),
            'data' => $allData
        ]);
    }
}