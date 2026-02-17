<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class TestController extends Controller
{
    public function index()
    {
         $query = Order::with(relations: ['assignment.sopir', 'assignment.mobil', 'penumpang'])
        ->whereHas('assignment')
        ->orderBy('order.waktu_penjemputan', 'asc');
        $result = $query->get();
        return $result;
        return response()->json([
            'status' => true,
            'message' => "Berhasil",
            'data' => $result,
            'count' => $result->count()
        ]);
    }
}
