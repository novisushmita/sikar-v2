<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pengguna;
use App\Models\ReviewSopir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\CarAvailabilityTrait;
use App\Traits\DataSopirTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

        $nomorKepalaSopir = Pengguna::where('role', 'kepala_sopir')->first()->nomor;

        $orders->each(function($order) use ($nomorKepalaSopir) {
            $order->nomor_kepala_sopir = $nomorKepalaSopir;
        });

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
    /**
     * 
     * */    
    public function submitReview(Request $request)
    {
        // Cek kebenaran data review
        try {
            $validated = $request->validate([
                'review' => 'required|integer|max:5',
                'tanggal' => 'required|date',
                'sopir_id' => 'required|exists:sopir,sopir_id',
            ], [
                'review.required' => 'Review wajib diisi',
                'review.max' => 'Review maksimal 5 bintang',
                'tanggal.required' => 'Tanggal wajib diisi',
                'sopir_id.required' => 'Sopir wajib diisi',
            ]); 
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }
        
        // Transaksi order
        DB::beginTransaction();
        
        try {
            $user = $request->auth_user;
            
            // Insert data order
            ReviewSopir::create([
                'pengguna_id' => $user->pengguna_id,
                'review' => $validated['review'],
                'tanggal' => $validated['tanggal'],
                'sopir_id' => $validated['sopir_id'],
            ]);

            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => 'Review berhasil disubmit',
            ], 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
