<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->auth_user;
        
        // Dashboard kepala sopir
        if ($user->isKepalaSopir()) {
            return response()->json([
                'status' => true,
                'message' => 'Dashboard Kepala Sopir',
                'data' => [
                    'user' => $user->name,
                    'token'=> $user->token,
                    'role'=> $user->role,
                    'nomor'=> $user->nomor,
                    'menu' => [
                        'Pesanan',
                        'Riwayat',
                        'Ketersediaan',
                        'Peringkat'
                    ]
                ]
            ]);
        }
        
        // Dashboard sopir
        if ($user->isSopir()) {
            return response()->json([
                'status' => true,
                'message' => 'Dashboard Sopir',
                'data' => [
                    'user' => $user->name,
                    'token'=> $user->token,
                    'role'=> $user->role,
                    'nomor'=> $user->nomor,
                    'menu' => [
                        'Pesanan',
                        'Riwayat',
                        'Peringkat'
                    ]
                ]
            ]);
        }
        
        // Dashboard penumpang
        if ($user->isPenumpang()) {
            $pendingOrder = $user->orders()->where('status', 'pending')->count();
            $onProcessOrder = $user->orders()->where('status', 'on-process')->count();
            
            return response()->json([
                'status' => true,
                'message' => 'Dashboard Penumpang',
                'data' => [
                    'user' => [
                        'id' => $user->pengguna_id,
                        'name' => $user->name,
                        'token'=> $user->token,
                        'role'=> $user->role,
                        'nomor'=> $user->nomor,
                    ],
                    'statistik' => [
                        'pending' => $pendingOrder,
                        'on_process' => $onProcessOrder,
                    ],
                    'menu' => [
                        'Pemesanan',
                        'Pemantauan',
                        'Riwayat',
                        'Ketersediaan'
                    ]
                ]
            ]);
        }
    }
}