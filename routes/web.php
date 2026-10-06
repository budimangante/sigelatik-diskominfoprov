<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
// Route::get('/admin', function () {
    //     return view('admin.index');
    // })->name('admin.index');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::middleware(['auth'])->group(function () {

    //DASHBOARD KHUSUS ADMINISTRATOR
    Route::middleware('role:super_admin')->prefix('superadmin')->group(function () {
        Route::get('/', function () {
            return view('superadmin.index'); // resources/views/superadmin/dashboard.blade.php
        })->name('superadmin.index');
    });

    Route::middleware('role:admin_default')->prefix('admin')->group(function () {
        Route::get('/', function () {
            return view('admin.index');
        })->name('admin.index');
    });

    // DASHBOARD KHUSUS TEKNISI & BIDANG (PEGAWAI BKD)
    Route::middleware('role:helpdesk_bidang,helpdesk_teknisi')->prefix('teknis')->group(function () {
        Route::get('/dashboard', function () {
            return view('teknis.dashboard'); // resources/views/teknis/dashboard.blade.php
        })->name('teknis.dashboard');
    });

    // DASHBOARD KHUSUS OPD / HELPDESK UTAMA
    Route::middleware('role:helpdesk_utama')->prefix('opd')->group(function () {
        Route::get('/dashboard', function () {
            return view('opd.dashboard'); // resources/views/opd/dashboard.blade.php
        })->name('opd.dashboard');
    });

    // DASHBOARD KHUSUS MASYARAKAT UMUM
    Route::middleware('role:masyarakat')->prefix('layanan')->group(function () {
        Route::get('/dashboard', function () {
            return view('layanan.dashboard'); // resources/views/layanan/dashboard.blade.php
        })->name('layanan.dashboard');
    });

});

require __DIR__.'/settings.php';
