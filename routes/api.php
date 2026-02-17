<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\KepalaSopirController;
use App\Http\Controllers\PenumpangController;
use App\Http\Controllers\SopirController;

// ============================================
// PUBLIC ROUTES
// ============================================

Route::post('/login', [AuthController::class, 'login']);

// ============================================
// PROTECTED ROUTES
// ============================================

Route::middleware('auth.token')->group(function () {
    // Data user login
    Route::post('/me', [AuthController::class, 'me']);
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
});

// ============================================
// ROUTES KHUSUS PENUMPANG
// ============================================

Route::middleware(['auth.token:penumpang'])->group(function () {
    // Orders penumpang yang login
    Route::get('/penumpang/orders', [PenumpangController::class, 'getMyOrders']);
    // Detail order pernumpang yang login
    Route::get('/penumpang/orders/{id}', [PenumpangController::class, 'getDetailOrder']);
    // Mobil tersedia
    Route::get('/penumpang/mobil', [PenumpangController::class, 'checkAvailCar']);
    // Sopir tersedia
    Route::get('/penumpang/sopir', [PenumpangController::class, 'getSopir']);
    // Create order
    Route::post('/penumpang/create', [FormController::class, 'createOrder']);
    // Cancel order
    Route::delete('/penumpang/cancel/{id}', [FormController::class, 'cancelOrder']);
    // Confirm order
    Route::post('/penumpang/confirm/{id}', [FormController::class, 'confirmOrder']);
});

// ============================================
// ROUTES KHUSUS SOPIR
// ============================================

Route::middleware(['auth.token:sopir'])->group(function () {
    // Mulai perjalanan
    // Route::post('/mulai-perjalanan', [PerjalananController::class, 'start']);
    // Kehadiran
    Route::post('/sopir/kerja', [SopirController::class, 'toggleMasukKerja']);
    Route::get('/sopir/kerja', [SopirController::class, 'getStatusKerja']);
    Route::get('/sopir/orders', [SopirController::class, 'getOrderSopir']);
    // Leaderboard
    Route::get('/sopir/leaderboard', [SopirController::class, 'getLeaderboard']);
    Route::post('/sopir/start/{id}', [SopirController::class, 'startOrder']);
    Route::post('/sopir/complete/{id}', [SopirController::class, 'completeOrder']);
});

// ============================================
// ROUTES KHUSUS KEPALA SOPIR
// ============================================

Route::middleware(['auth.token:kepala_sopir'])->group(function () {
    // Leaderboard
    Route::get('/kepalasopir/leaderboard', [KepalaSopirController::class, 'getLeaderboard']);
    Route::get('/kepalasopir/order', [KepalaSopirController::class, 'getOrder']);
    // Mobil tersedia
    Route::get('/kepalasopir/mobil', [KepalaSopirController::class, 'checkAvailCar']);
    // Sopir tersedia
    Route::get('/kepalasopir/sopir', [KepalaSopirController::class, 'getSopir']);
    Route::get('/kepalasopir/sopirmasuk', [KepalaSopirController::class, 'getSopirMasukKerja']);
    Route::post('/kepalasopir/assign', [KepalaSopirController::class, 'assignOrder']);
    Route::delete('/kepalasopir/reject/{id}', [KepalaSopirController::class, 'rejectOrder']);
    Route::get('/kepalasopir/export-presensi-sopir', [KepalaSopirController::class, 'exportPresensiSopir'])
        ->name('kepalasopir.export.presensi.driver');
});