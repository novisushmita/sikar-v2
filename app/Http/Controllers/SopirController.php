<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sopir;
use App\Models\Order;
use App\Models\Mobil;
use App\Models\HistoryBekerjaSopir;
use App\Traits\LeaderboardTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use Exception;

class SopirController extends Controller
{
    // Leaderboard
    use LeaderboardTrait;

    // Kehadiran
    public function toggleMasukKerja(Request $request)
    {
        DB::beginTransaction();
        try {

            $user = $request->auth_user;

            // Cari data sopir
            $sopir = Sopir::where('sopir_id', $user->pengguna_id)->first();

            if (!$sopir) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data sopir tidak ditemukan'
                ], 404);
            }

            // Jika masuk kerja 0 maka new status = 1 begitupun sebaliknya.
            $newStatus = $sopir->masuk_kerja == 0 ? 1 : 0;

            // Jika mau berhenti bekerja, cek order ongoing
            if ($newStatus == 0 && $sopir->order_ongoing > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak bisa berhenti bekerja, masih ada pesanan yang aktif'
                ], 400);
            }

            
            $sumTotalOrder = 0;
            $historyToday = HistoryBekerjaSopir::where('sopir_id', $sopir->sopir_id)
                ->where('tanggal', Carbon::today())
                ->first();
            if ($historyToday) {
                $sumTotalOrder = $sopir->order_completed + $historyToday->order_completed;
            } else {
                $sumTotalOrder = $sopir->order_completed;
            }

            // Insert saat masuk kerja / Update saat pulang kerja (prevent duplicate) -> data tetap bisa untuk monitoring
            HistoryBekerjaSopir::updateOrCreate(
                [
                    'sopir_id' => $sopir->sopir_id,
                    'tanggal' => Carbon::today(),
                ],
                [
                    'order_completed' => $sumTotalOrder 
                ]
            );
            
            // update status bekerja
            $sopir->masuk_kerja = $newStatus;

            // reset jumlah order selesai (saat mulai masuk kerja)
            $sopir->order_completed = 0;
            $sopir->save();

            DB::commit();
            
            // Menampilkan pesan
            $message = $newStatus == 1 ? 'Berhasil masuk kerja' : 'Berhasil pulang kerja';

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => [
                    'sopir_id' => $sopir->sopir_id,
                    'nama' => $sopir->name,
                    'masuk_kerja' => $sopir->masuk_kerja,
                    'status_text' => $newStatus == 1 ? 'Sedang Kerja' : 'Tidak Kerja',
                    'order_completed' => $sopir->order_completed,
                    'order_ongoing' => $sopir->order_ongoing
                ]
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate status bekerja: ' . $e->getMessage()
            ], 500);
        }
    }

    // Data order yang di assign ke sopir dan riwayat order sopir
    public function getOrderSopir(Request $request)
    {
        $user = $request->auth_user;

        // Cek apakah status yang di request valid
        $validator = Validator::make($request->all(), [
            'status' => 'in:assigned,on-process,confirmed,completed,all',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Parameter tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }
        // Ambil data order, assignment, sopir, mobil, penumpang, dimana data orderan mempunyai assignmentnya dengan sopir_id nya adalah sopir yang login
        $query = Order::with(['assignment.sopir', 'assignment.mobil', 'penumpang', 'review'])
        ->whereHas('assignment', function($query) use ($user) {
            $query->where('sopir_id', $user->pengguna_id);
        });

        // Data riwayat harus menambahkan parameter status all
         if ($request->has('status')) {
            if (trim($request->status) == 'all') {
                $query->riwayatSopir();
            } else {
                $query->where('status', $request->status);
            }
        } else {
            $query->outstandingSopir();
        }

        $order = $query->orderBy('status', 'asc')->get();

         if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan!'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data order sopir berhasil diambil',
            'count' => $order->count(),
            'data' => $order
        ], 200);
    }

    public function startOrder(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $user = $request->auth_user;

            $order = Order::lockForUpdate()->findOrFail($id);
            $sopir = Sopir::lockForUpdate()->findOrFail($user->pengguna_id);
            $assignment = $order->assignment;

            if ($assignment->sopir_id !== $user->pengguna_id) {
                throw new Exception('Anda tidak memiliki akses ke order ini');
            }

            // Hanya bisa start jika status assigned
            if ($order->status !== Order::STATUS_ASSIGNED) {
                throw new Exception(
                    'Order hanya bisa dimulai saat status assigned. Status saat ini: ' . $order->status
                );
            }

            if ($sopir->masuk_kerja !== 1) {
                throw new Exception(
                    'Order hanya bisa dimulai saat status sopir sudah bekerja. Status sopir saat ini: ' . $sopir->masuk_kerja ? "Sudah bekerja" : "Belum bekerja"
                );
            }

            $order->status = Order::STATUS_ON_PROCESS;
            $order->updated_at = Carbon::now();
            $order->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Perjalanan berhasil dimulai'
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal memulai perjalanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatusKerja(Request $request)
    {
        try {
            $user = $request->auth_user;

            $sopir = Sopir::where('name', $user->name)->first();

            if (!$sopir) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data sopir tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data status kerja berhasil diambil',
                'data' => [
                    'sopir_id' => $sopir->sopir_id,
                    'nama' => $sopir->name,
                    'masuk_kerja' => $sopir->masuk_kerja,
                    'status_text' => $sopir->masuk_kerja == 1 ? 'Sedang Kerja' : 'Tidak Kerja',
                    'order_completed' => $sopir->order_completed,
                    'order_ongoing' => $sopir->order_ongoing
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data status kerja: ' . $e->getMessage()
            ], 500);
        }
    }
}
