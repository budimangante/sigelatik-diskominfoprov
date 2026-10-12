<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

//Route::inertia('/', 'Welcome')->name('home');
Route::get('/beranda', function () {
    return view('beranda.index-beranda'); 
})->name('beranda');

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

// === TAMBAHKAN ROUTE REGISTRASI DI SINI ===
Route::get('/registrasi', function () {
    // Sesuaikan 'auth.register' jika kamu menaruh filenya di resources/views/auth/register.blade.php
    return view('auth.registrasi'); 
})->name('registrasi');

Route::post('/registrasi', [AuthController::class, 'registrasi'])->name('registrasi.post');
// ==========================================

Route::middleware(['auth'])->group(function () {

    //DASHBOARD KHUSUS ADMINISTRATOR
    Route::middleware('role:super_admin')->prefix('superadmin')->group(function () {
        Route::get('/', function () {
            return view('superadmin.index'); // resources/views/superadmin/dashboard.blade.php
        })->name('superadmin.index');

        Route::get('/profil/tentang-kami', function () {
            return view('superadmin.profil-tentang');
        })->name('profil.tentang');

        Route::get('/profil/sub-layanan', function () {
            return view('superadmin.profil-sub');
        })->name('profil.sub');

        Route::get('/profil/team-infrastruktur', function () {
            return view('superadmin.profil-team');
        })->name('profil.team');

        Route::get('/profil/link-layanan', function () {
            return view('superadmin.profil-link');
        })->name('profil.link');

        Route::get('/data/VPS', function () {
            return view('superadmin.data-vps');
        })->name('data.vps');

        Route::get('/data/WHM', function () {
            return view('superadmin.data-whm');
        })->name('data.whm');

        Route::get('/metro/jaringan', function () {
            return view('superadmin.metro-jaringan');
        })->name('metro.jaringan');

        Route::get('/device/software', function () {
            return view('superadmin.device-software');
        })->name('device.software');

        Route::get('/device/hardware', function () {
            return view('superadmin.device-hardware');
        })->name('device.hardware');

        Route::get('/serangan', function () {
            return view('superadmin.serangan');
        })->name('serangan');

        Route::get('/gangguan', function () {
            return view('superadmin.gangguan');
        })->name('gangguan');

        Route::get('/kategori-ticket', function () {
            return view('superadmin.kategori-ticket');
        })->name('kategori.ticket');

        Route::get('/prioritas-ticket', function () {
            return view('superadmin.prioritas-ticket');
        })->name('prioritas.ticket');

        Route::get('/daftar-ticket', function () {
            return view('superadmin.daftar-ticket');
        })->name('daftar.ticket');

        Route::get('/perangkat', function () {
            return view('superadmin.perangkat');
        })->name('perangkat');

        Route::get('/bidang', function () {
            return view('superadmin.bidang');
        })->name('bidang');

        Route::get('/notifikasi', function () {
            return view('superadmin.notifikasi');
        })->name('notifikasi');

        Route::get('/pengguna', function () {
            return view('superadmin.pengguna');
        })->name('pengguna');

    });

    Route::middleware('role:admin_default')->prefix('admin')->group(function () {
        Route::get('/', function () {
            return view('admin.index');
        })->name('admin.index');

        Route::get('/monitoring', function () {
            return view('admin.monitoring');
        })->name('admin.monitoring');

        Route::get('/kinerja', function () {
            return view('admin.kinerja');
        })->name('admin.kinerja');

        Route::get('/laporan', function () {
            return view('admin.laporan');
        })->name('admin.laporan');
    });

    // DASHBOARD KHUSUS TEKNISI & BIDANG (PEGAWAI BKD)
    Route::middleware('role:helpdesk_teknisi')->prefix('teknisi')->group(function () {
        Route::get('/', function () {
            return view('teknisi.index'); // resources/views/teknisi/teknisi.blade.php
        })->name('teknisi.index');

        Route::get('/history-teknisi', function () {
            return view('teknisi.history-teknisi'); // resources/views/teknisi/history-teknisi.blade.php
        })->name('teknisi.history');
    });

    // DASHBOARD KHUSUS BIDANG (PEGAWAI BIDANG TIK)
    Route::middleware('role:helpdesk_bidang')->prefix('bidang')->group(function () {
        Route::get('/', function () {
            return view('bidang.index'); // resources/views/bidang/bidang.blade.php
        })->name('bidang.index');

        Route::get('/forward', function () {
            return view('bidang.forward');
        })->name('bidang.forward');

        Route::get('/history', function () {
            return view('bidang.history-ticket');
        })->name('bidang.history');

        Route::get('/kategori', function () {
            return view('bidang.kategori');
        })->name('bidang.kategori');
    });

    // DASHBOARD KHUSUS OPD / HELPDESK UTAMA
    Route::middleware('role:helpdesk_utama')->prefix('dinas')->group(function () {
        Route::get('/', function () {
            return view('dinas.index'); // resources/views/dinas/dinas.blade.php
        })->name('dinas.index');

        Route::get('/triage', function () {
            return view('dinas.triage');
        })->name('dinas.triage');

        Route::get('/sop', function () {
            return view('dinas.sop');
        })->name('dinas.sop');
    });

    // DASHBOARD KHUSUS MASYARAKAT UMUM
    Route::middleware('role:masyarakat')->prefix('user')->group(function () {
        Route::get('/', function () {
            return view('user.index'); // resources/views/user/index.blade.php
        })->name('user.index');
        
        Route::get('/tiket', function () {
            return view('user.tiket');
        })->name('user.tiket');

        Route::get('/chat', function () {
            return view('user.chat');
        })->name('user.chat');
    });

});

require __DIR__.'/settings.php';
