<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sopir;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Validator;
use App\Traits\CarAvailabilityTrait;
use App\Traits\DataSopirTrait;

use Exception;

class PenumpangController extends Controller
{
    // Mobil tersedia
    use CarAvailabilityTrait;

    // Sopir tersedia
    use DataSopirTrait;

    /**
     * Get orders milik penumpang yang login
     * Filter status (default pending, on-process, dan assigned)
     */
    public function getMyOrders(Request $request)
    {
        $user = $request->auth_user;

        // Cek apakah status yang di request valid
        $validator = Validator::make($request->all(), [
            'status' => 'in:pending,assigned,on-process,confirmed,completed,canceled,rejected,all',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Parameter tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Query orders milik user yang login
        $query = Order::with(['penumpang', 'assignment.sopir', 'assignment.mobil'])
            ->where('pengguna_id', $user->pengguna_id);

        // Filter berdasarkan status jika ada
        if ($request->has('status')) {
            if (trim($request->status) == 'all') {
                $query->riwayatPenumpang();
            } else {
                $query->where('status', $request->status);
            }
        } else {
            $query->active();
        }

        // Urutkan dari terbaru
        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data order berhasil diambil',
            'total' => $orders->count(),
            'data' => $orders
        ], 200);
    }

    /**
     * Get orders outstanding milik penumpang yang login
     */
    public function getDetailOrder(Request $request, $id)
    {
        $user = $request->auth_user;

        // Ambil data order
        $detail = Order::with(['assignment.sopir','assignment.mobil', 'penumpang'])
            ->where('order_id', $id)
            ->where('pengguna_id', $user->pengguna_id)
            ->first();

        // Hasil jika data order tidak ada
        if (!$detail) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan!'
            ], 404);
        }

        // Hasil jika data order ada
        return response()->json([
            'status' => true,
            'message' => 'Data order berhasil diambil',
            'data' => $detail
        ], 200);
    }
}
