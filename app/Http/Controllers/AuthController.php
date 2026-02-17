<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private function redirectByRole($role)
    {
        switch ($role) {
            case 'penumpang':
                return redirect('/penumpang/pemesanan');
            case 'sopir':
                return redirect('/sopir/pesanan');
            case 'kepala_sopir':
                return redirect('/kepalasopir/pesanan');
            default:
                return redirect()->route('login');
        }
    }
    public function index()
    {
        if (session('token')) {
            $role = session('role');
            return $this->redirectByRole($role);
        }
        return view('login');
    }
    /**
     * Login
     */
    public function login(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => 'required|string',
                'token' => 'required|string'
            ]);

            // Cari pengguna
            $pengguna = Pengguna::where('name', $request->name)
                                ->where('token', $request->token)
                                ->first();

            if ($pengguna) {
                // Set session
                session([
                    'token' => $pengguna->token,
                    'pengguna_id' => $pengguna->pengguna_id,
                    'name' => $pengguna->name,
                    'role' => $pengguna->role
                ]);

                // Force save session
                session()->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Login berhasil',
                    'data' => [
                        'token' => $pengguna->token,
                        'pengguna_id' => $pengguna->pengguna_id,
                        'name' => $pengguna->name,
                        'role' => $pengguna->role
                    ]
                ], 200);
            } else {
                // Login gagal
                return response()->json([
                    'status' => false,
                    'message' => 'Nama atau token salah'
                ], 401);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Data user login
     * Route sudah diprotect dengan middleware
     */
    public function me(Request $request)
    {
        $pengguna = $request->auth_user;

        return response()->json([
            'status' => true,
            'data' => [
                'pengguna_id' => $pengguna->pengguna_id,
                'name'        => $pengguna->name,
                'role'        => $pengguna->role,
            ]
        ], 200);
    }

    /**
     * Logout
     * Route sudah diprotect dengan middleware
     */
    public function logout(Request $request)
    {
        $pengguna = $request->auth_user ?? null;
        $userName = session('name');
        
        // Hapus semua session
        $request->session()->flush();
        
        // Regenerate session ID untuk keamanan
        $request->session()->regenerate();
        
        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil',
            'user' => $userName ?? $pengguna->name ?? 'User'
        ], 200);
    }
}