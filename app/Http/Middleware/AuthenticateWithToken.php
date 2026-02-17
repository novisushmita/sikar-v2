<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithToken
{
    //Menangani permintaan masuk dengan validasi token dan role (opsional)
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        //Ambil token prioritas dari Header Authorization (Bearer token)
        //Jika tidak ada, ambil dari body request
        $token = $request->header('Authorization') 
            ? str_replace('Bearer ', '', $request->header('Authorization'))
            : null;

        // Prioritas 2: Ambil dari body request
        if (!$token) {
            $token = $request->input('token');
        }

        // Prioritas 3: Ambil dari session
        if (!$token) {
            $token = session('token');
        }

        // Prioritas 4: Ambil dari query string (untuk backward compatibility)
        if (!$token) {
            $token = $request->query('token');
        }

        //Jika token tidak ada maka tolak request
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }

        // Cari pengguna berdasarkan token
        $pengguna = Pengguna::where('token', $token)->first();

        //Jika token tidak valid maka tolak request
        if (!$pengguna) {
            // Hapus session jika ada
            session()->forget(['token', 'pengguna_id', 'name', 'role']);
            
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid.'
            ], 401);
        }

        // Cek role jika middleware dipanggil dengan parameter role
        if (!empty($roles) && !in_array($pengguna->role, $roles)) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki hak akses untuk fitur ini.',
                'required_role' => $roles,
                'your_role' => $pengguna->role
            ], 403);
        }

        // Simpan/update session data
        session([
            'token' => $pengguna->token,
            'pengguna_id' => $pengguna->pengguna_id,
            'name' => $pengguna->nama,
            'role' => $pengguna->role
        ]);

        // Simpan data user ke request agar bisa diakses di controller
        $request->merge(['auth_user' => $pengguna]);

        // Lanjutkan ke controller
        return $next($request);
    }
}