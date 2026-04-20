<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Mobil;
use App\Models\Sopir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;
use App\Services\FcmService;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Log;


class FormController extends Controller
{
    /**
     * Buat order baru
     */
    public function createOrder(Request $request)
    {
        // Cek kebenaran data order
        try {
            $validated = $request->validate([
                'tempat_penjemputan' => 'required|string|max:255',
                'tempat_tujuan' => 'required|string|max:255',
                'waktu_penjemputan' => 'required|date|after:now',
                'keterangan' => 'required|string|max:500'
            ], [
                'tempat_penjemputan.required' => 'Tempat penjemputan wajib diisi',
                'tempat_penjemputan.max' => 'Tempat penjemputan maksimal 255 karakter',
                
                'tempat_tujuan.required' => 'Tempat tujuan wajib diisi',
                'tempat_tujuan.max' => 'Tempat tujuan maksimal 255 karakter',
                
                'waktu_penjemputan.required' => 'Waktu penjemputan wajib diisi',
                'waktu_penjemputan.date' => 'Format waktu penjemputan tidak valid',
                'waktu_penjemputan.after' => 'Format Waktu penjemputan harus di masa depan',
                
                'keterangan.max' => 'Keterangan maksimal 500 karakter'
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
            
            // Cek data order yang masih aktif
            $activeOrder = Order::byPengguna($user->pengguna_id)
                ->whereIn('status', ['pending', 'assigned', 'on-process'])
                ->exists();
            
            if ($activeOrder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda masih memiliki order aktif'
                ], 400);
            }
            
            // Insert data order
            $order = Order::create([
                'pengguna_id' => $user->pengguna_id,
                'tempat_penjemputan' => $validated['tempat_penjemputan'],
                'tempat_tujuan' => $validated['tempat_tujuan'],
                'waktu_penjemputan' => $validated['waktu_penjemputan'],
                'keterangan' => $validated['keterangan'] ?? '-',
                'status' => Order::STATUS_PENDING // Status default
            ]);

            $order->load('penumpang');
            
            DB::commit();

            try {
                $tokens = Pengguna::where('role', Pengguna::ROLE_KEPALA_SOPIR)
                    ->whereNotNull('web_token')
                    ->pluck('web_token')
                    ->toArray();

                if (!empty($tokens)) {
                    (new FcmService())->kirimKeBanyak(
                        $tokens,
                        'Ada Orderan Baru!',
                        $user->name . ' memesan. Segera assign sopir!'
                    );
                }
             } catch (Exception $fcmError) {
                Log::error('FCM Error: ' . $fcmError->getMessage());
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Order berhasil dibuat',
                'data' => $order,
            ], 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order (jika status pending atau assigned)
     */
    public function cancelOrder(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $user = $request->auth_user;
            
            $order = Order::lockForUpdate()->findOrFail($id);
            
            // Hanya bisa cancel order sendiri
            if ($order->pengguna_id !== $user->pengguna_id) {
                throw new Exception('Anda tidak memiliki akses ke order ini');
            }
            
            // Hanya bisa cancel jika status pending
            if (!in_array($order->status, [Order::STATUS_PENDING])) {
                throw new Exception('Order hanya bisa dibatalkan saat status pending. Status saat ini: ' . $order->status);
            }
            
            // Update status order menjadi canceled
            $order->status = Order::STATUS_CANCELED;
            $order->save();
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => 'Order berhasil dibatalkan'
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Gagal membatalkan order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm order (jika status on-process)
     */
    public function confirmOrder(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $user = $request->auth_user;
            
            $order = Order::with('assignment')->lockForUpdate()->findOrFail($id);
            $sopir = Sopir::lockForUpdate()->findOrFail($order->assignment->sopir_id);
            $mobil = Mobil::lockForUpdate()->findOrFail($order->assignment->mobil_id);
            // Hanya bisa confirm order sendiri
            if ($order->pengguna_id !== $user->pengguna_id) {
                throw new Exception('Anda tidak memiliki akses ke order ini');
            }
            
            // Hanya bisa confirm jika status on-process
            if (!in_array($order->status, [Order::STATUS_ON_PROCESS])) {
                throw new Exception('Order hanya bisa dikonfirmasi saat status on-process. Status saat ini: ' . $order->status);
            }
            
            // Update status order menjadi confirmed
            $order->status = Order::STATUS_CONFIRMED;
            $order->updated_at = Carbon::now();
            $order->save();

            // Tambah jumlah order completed
            $sopir->increment('order_completed');

            // Reset ketersediaan sopir
            $sopir->update(['order_ongoing' => 0]);
            
            // Reset ketersediaan mobil
            $mobil->update(['availability' => 1]);
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => 'Order berhasil dikonfirmasi'
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengkonfirmasi order: ' . $e->getMessage()
            ], 500);
        }
    }
}
