<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenWebController extends Controller
{
    public function __invoke(Request $request)
    {
        $pengguna = $request->input('auth_user'); // ✅ ambil dari sini

        $pengguna->update([
            'web_token' => $request->web_token
        ]);

        return response()->json([], 201);
    }
}