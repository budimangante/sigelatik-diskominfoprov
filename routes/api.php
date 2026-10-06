<?php
use Illuminate\Http\Request;    
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AdminDashboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn(Request $request) => $request->user());

    // Grup Masyarakat
    Route::middleware('role:masyarakat')->prefix('masyarakat')->group(function () {
        Route::get('/dashboard', fn() => response()->json(['data' => 'Dashboard Pelapor']));
    });

    // Grup Tim Teknis PTSP (Helpdesk Bidang & Teknisi)
    Route::middleware('role:helpdesk_bidang,helpdesk_teknisi')->prefix('teknis')->group(function () {
        Route::get('/tickets', fn() => response()->json(['data' => 'Daftar Tiket Teknis']));
    });

    // Grup Helpdesk Utama OPD
    Route::middleware('role:helpdesk_utama')->prefix('opd')->group(function () {
        Route::get('/tickets/pending', fn() => response()->json(['data' => 'Tiket OPD']));
    });

    // Grup Administrator (Super Admin & Admin Default)
    Route::middleware('role:super_admin,admin_default')->prefix('admin')->group(function () {
        Route::get('/management', fn() => response()->json(['data' => 'Panel Konfigurasi']));
    });

    Route::middleware(['auth:sanctum', 'role:super_admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard-data', [AdminDashboardController::class, 'index']);
    });
    
    Route::middleware(['auth:sanctum', 'role:admin_default,super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard-data', [AdminDashboardController::class, 'index']);
    });

    // DUMMY SSO PTSP SERVER (Khusus simulasi/pengujian lokal)
    Route::post('/mock-ptsp-sso', function (\Illuminate\Http\Request $request) {
    if ($request->nip === '199501012022011001' && $request->password === 'secret123') {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'nip'       => '199501012022011001',
                'nama'      => 'Ahmad Teknisi PTSP',
                'instansi'  => 'DPMPTSP',
                'role_ptsp' => 'teknisi',
            ]
        ], 200);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
});
});