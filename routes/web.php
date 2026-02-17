<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

// halaman login
Route::get('/', [AuthController::class, 'index'])
    ->name('login');

// proses login
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

// logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth.token')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PENUMPANG
    |--------------------------------------------------------------------------
    */
    Route::prefix('penumpang')->group(function () {
        Route::get('/pemesanan', fn () => view('penumpang.pemesanan'))
            ->name('penumpang.pemesanan');
        
        Route::get('/monitoring', fn () => view('penumpang.pemantauan'))
            ->name('penumpang.pemantauan');

        Route::get('/riwayat', fn () => view('penumpang.riwayat'))
            ->name('penumpang.riwayat');
        
        Route::get('/ketersediaan', fn () => view('penumpang.ketersediaan'))
            ->name('penumpang.ketersediaan');
    });

    /*
    |--------------------------------------------------------------------------
    | SOPIR
    |--------------------------------------------------------------------------
    */
    Route::prefix('sopir')->group(function () {
        Route::get('/pesanan', fn () => view('sopir.pesanan'))
            ->name('sopir.pesanan');

        Route::get('/riwayat', fn () => view('sopir.riwayat'))
            ->name('sopir.riwayat');

        Route::get('/peringkat', fn () => view('sopir.peringkat'))
            ->name('sopir.peringkat');
    });

    /*
    |--------------------------------------------------------------------------
    | KEPALA SOPIR
    |--------------------------------------------------------------------------
    */
    Route::prefix('kepalasopir')->group(function () {
        Route::get('/pesanan', fn () => view('kepalasopir.pesanan'))
            ->name('kepalasopir.pesanan');

        Route::get('/riwayat', fn () => view('kepalasopir.riwayat'))
            ->name('kepalasopir.riwayat');

        Route::get('/ketersediaan', fn () => view('kepalasopir.ketersediaan'))
            ->name('kepalasopir.ketersediaan');
        
        Route::get('/peringkat', fn () => view('kepalasopir.peringkat'))
            ->name('kepalasopir.peringkat');

        Route::get('/presensi', fn () => view('kepalasopir.presensi'))
            ->name('kepalasopir.presensi');
    });

});