<?php

namespace App\Http\Controllers;

use App\Exports\PresensiSopirExport;
use App\Models\Mobil;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\Sopir;
use App\Models\HistoryBekerjaSopir;
use App\Exports\RekapOrderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\CarAvailabilityTrait;
use App\Traits\LeaderboardTrait;
use App\Traits\DataSopirTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use Exception;

class KepalaSopirController extends Controller
{
    // Leaderboard
    use LeaderboardTrait;

    // Mobil tersedia
    use CarAvailabilityTrait;

    // Sopir tersedia
    use DataSopirTrait;

    public function getOrder(Request $request)
    {
        try {
            // Filter berdasarkan status jika ada | jika tidak ada get data pending yang perlu di assign
            if ($request->has('status')) {
                $query = Order::with(['assignment.sopir', 'assignment.mobil', 'penumpang']);
                if (trim($request->status) === 'all') {
                    $query = $query->riwayatKepalaSopir();
                } else {
                    $query = $query->where('status', $request->status);
                }
            } else {
                $query = Order::with('penumpang');
                $query = $query->outstandingKepalaSopir();
            }

            $order = $query->orderBy('waktu_penjemputan', 'asc')->get();

            return response()->json([
                'status' => true,
                'message' => "Data order kepala sopir berhasil diambil",
                'count' => $order->count(),
                'data' => $order
            ]);
        } catch (Exception $e) {
             return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data order kepala sopir',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMobil()
    {
        try {
            $mobil = Mobil::available()
            ->orderBy('mobil_id', 'asc')
            ->get();

            return response()->json([
                'status' => true,
                'message' => 'Data mobil yang tersedia berhasil diambil',
                'count' => $mobil->count(),
                'data' => $mobil
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data mobil yang tersedia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSopirMasukKerja(Request $request)
    {
        $request->validate([
            'startDate' => 'nullable|date',
            'endDate'   => 'nullable|date|after_or_equal:startDate',
        ]);

        try {
            $query = HistoryBekerjaSopir::with('sopir');

           $startDate = $request->startDate
                ? Carbon::parse($request->startDate)->startOfDay()
                : null;

            $endDate = $request->endDate
                ? Carbon::parse($request->endDate)->endOfDay()
                : null;
        
            // Filter berdasarkan tanggal (jika ada)
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate,$endDate]);
            } elseif ($startDate) {
                // Jika hanya start date (dari tanggal ini sampai sekarang)
                $query->where('tanggal', '>=', $startDate);
            } elseif ($endDate) {
                // Jika hanya end date (sampai tanggal ini)
                $query->where('tanggal', '<=', $endDate);
            } else {
                $query->where('tanggal', Carbon::today());
            }

            $sopir = $query->orderBy('tanggal', 'desc')->get();

            return response()->json([
                'status' => true,
                'message' => 'Data presensi sopir berhasil diambil',
                'count' => $sopir->count(),
                'data' => $sopir
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data presensi sopir',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST - Assign order ke sopir dan mobil
     */
    public function assignOrder(Request $request)
    {
        // Validation
        // required => wajib diisi
        // exists => data harus ada di tabel dan kolom tersebut
        try {
            $request->validate([
                'order_id' => 'required|exists:order,order_id',
                'sopir_id' => 'required|exists:sopir,sopir_id',
                'mobil_id' => 'required|exists:mobil,mobil_id'
            ], [
                'order_id.required' => 'Order Id tidak ada',
                'sopir_id.required' => 'Sopir Id tidak ada',
                'mobil_id.required' => 'Mobil Id tidak ada',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }


        DB::beginTransaction();

        try {
            $order = Order::lockForUpdate()->findOrFail($request->order_id);

            if ($order->status !== 'pending') {
                throw new Exception('Order sudah di-assign atau tidak dalam status pending. Status order : ' . $order->status);
            }

            $sopir = Sopir::lockForUpdate()->findOrFail($request->sopir_id);

            if ($sopir->order_ongoing !== 0) {
                throw new Exception('Sopir tidak tersedia.');
            }

            $mobil = Mobil::lockForUpdate()->findOrFail($request->mobil_id);

            if ($mobil->availability == 0) {
                throw new Exception('Mobil tidak tersedia');
            }

            // Cek apakah order sudah pernah di-assign sebelumnya
            $existingAssignment = OrderAssignment::where('order_id', $request->order_id)->exists();

            if ($existingAssignment) {
                throw new Exception('Order sudah memiliki assignment');
            }

            // Update status order menjadi assigned
            $order->status = 'assigned';
            $order->save();

            // Insert ke tabel order_assignment
            $assignment = OrderAssignment::create([
                'order_id' => $request->order_id,
                'sopir_id' => $request->sopir_id,
                'mobil_id' => $request->mobil_id
            ]);

            // Update order ongoing sopir menjadi 1 (sedang mengerjakan order)
            $sopir->order_ongoing = 1;
            $sopir->save();

            // Update availability mobil menjadi 0 (tidak tersedia)
            $mobil->availability = 0;
            $mobil->save();


            // Load relationships untuk response
            $assignment->load(['order.penumpang', 'sopir', 'mobil']);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Order berhasil di-assign ke sopir dan mobil',
                'data' => [
                    'assignment' => $assignment,
                    'order' => $order,
                    'sopir' => $sopir,
                    'mobil' => $mobil
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal assign order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectOrder(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::with(['assignment'])
            ->where('order_id', $id)->first();

            // Hanya bisa cancel jika status pending atau assigned
            if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_ASSIGNED])) {
                throw new Exception('Order hanya bisa dibatalkan saat status pending atau assigned. Status saat ini: ' . $order->status);
            }

            // Jika sudah di-assign, kembalikan status sopir dan mobil
            if ($order->status === 'assigned' && $order->assignment) {
                $assignment = $order->assignment;

                // Kembalikan status sopir
                if ($assignment->sopir) {
                    $sopir = Sopir::lockForUpdate()->find($assignment->sopir_id);
                    if ($sopir) {
                        $sopir->order_ongoing = 0;
                        $sopir->save();
                    }
                }

                // Kembalikan availability mobil
                if ($assignment->mobil) {
                    $mobil = Mobil::lockForUpdate()->find($assignment->mobil_id);
                    if ($mobil) {
                        $mobil->availability = 1;
                        $mobil->save();
                    }
                }

                // Delete assignment
                $assignment->delete();
            }

            $order->status = Order::STATUS_REJECTED;
            $order->save();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Order berhasil di-reject'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal me-reject order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Excel dengan parameter filter
     */
    public function export(Request $request)
    {
        // Validasi input (optional tapi recommended)
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Ambil parameter dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Generate nama file dinamis
        $fileName = 'Rekap Order';

        if ($startDate && $endDate) {
            $fileName .= ' - ' . date('d-m-Y', strtotime($startDate)) .
                        '-sd-' . date('d-m-Y', strtotime($endDate));
        } elseif ($startDate) {
            $fileName .= ' - ' . date('d-m-Y', strtotime($startDate)) . '-sd- Sekarang';
        } elseif ($endDate) {
            $fileName .= ' Awal -sd-'. date('d-m-Y', strtotime($endDate));
        } else {
            $fileName .= ' - Semua Data';
        }

        $fileName .= '.xlsx';

        // Export dengan parameter
        return Excel::download(
            new RekapOrderExport($startDate, $endDate),
            $fileName
        );
    }

    public function exportPresensiSopir(Request $request)
    {
        // Validasi input (optional tapi recommended)
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Ambil parameter dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Generate nama file dinamis
        $fileName = 'Presensi Sopir';

        if ($startDate && $endDate) {
            $fileName .= ' - ' . date('Y-m-d', strtotime($startDate)) .
                        '-sd-' . date('Y-m-d', strtotime($endDate));
        } elseif ($startDate) {
            $fileName .= ' - ' . date('Y-m-d', strtotime($startDate)) . '-sd- Sekarang';
        } elseif ($endDate) {
            $fileName .= ' Awal -sd-'. date('Y-m-d', strtotime($endDate));
        } else {
            $fileName .= ' - Semua Data';
        }

        $fileName .= '.xlsx';

        // Export dengan parameter
        return Excel::download(
            new PresensiSopirExport($startDate, $endDate),
            $fileName
        );
    }
}
