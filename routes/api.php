<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| 1. Endpoint Publik (Tanpa Token)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/mock-ptsp-sso', function (Request $request) {
    if ($request->nip === '199501012022011001' && $request->password === 'secret123') {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'nip'       => '199501012022011001',
                'nama'      => 'Ahmad Teknisi PTSP',
                'instansi'  => 'DPMPTSP',
                'role_ptsp' => 'teknisi',
            ],
        ], 200);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn(Request $request) => response()->json([
        'user' => $request->user()->load('roles'),
    ]));

    Route::middleware('role:pelapor')->prefix('masyarakat')->group(function () {
        Route::get('/dashboard', fn() => response()->json(['data' => 'Dashboard Pelapor']));
    });

    Route::middleware('role:helpdesk_bidang,teknisi')->prefix('teknis')->group(function () {
        Route::get('/tickets', fn() => response()->json(['data' => 'Daftar Tiket Teknis']));
    });

    Route::middleware('role:helpdesk_utama')->prefix('opd')->group(function () {
        Route::get('/tickets/pending', fn() => response()->json(['data' => 'Tiket OPD']));
    });

    Route::middleware('role:super_admin,admin_pemprov')->prefix('admin')->group(function () {
        Route::get('/management', fn() => response()->json(['data' => 'Panel Konfigurasi']));
        Route::get('/dashboard-data', [AdminDashboardController::class, 'index']);
    });
});