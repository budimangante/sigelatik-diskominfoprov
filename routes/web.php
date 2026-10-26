<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\LacakTicketController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Controllers\Web\TicketMessageController;
use App\Http\Controllers\Web\TeknisiController;
use App\Http\Controllers\Web\ForwardController;
use App\Http\Controllers\Web\BidangController;

// Rute Publik
Route::get('/{section?}', function ($section = null) {
    return view('beranda.index-beranda', ['activeSection' => $section]);
})->where('section', 'tentang|statistik|layanan')->name('beranda');

Route::match(['get', 'post'], '/lacak-tiket', [LacakTicketController::class, 'track'])->name('ticket.track');

// Alias untuk kebutuhan Wayfinder & komponen Vue Starter Kit
Route::get('/home', fn () => redirect()->route('beranda'))->name('home');
// Rute cerdas Dashboard: Mengarahkan otomatis ke dashboard role yang sedang aktif
Route::get('/dashboard', function () {
    if (! Auth::guard()->check()) {
        return redirect()->route('login');
    }

    /** @var \App\Models\User $user */
    $user = Auth::guard()->user();
    $user->loadMissing('roles');

    $roleMap = [
        'super_admin'     => 'superadmin.index',
        'admin_pemprov'   => 'admin.index',
        'helpdesk_utama'  => 'dinas.index',
        'helpdesk_bidang' => 'bidang.index',
        'teknisi'         => 'teknisi.index',
        'pelapor'         => 'user.index',
    ];

    foreach ($roleMap as $role => $routeName) {
        if ($user->hasRole($role)) {
            return redirect()->route($routeName);
        }
    }

    return redirect()->route('beranda');
})->name('dashboard');

// Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/registrasi', [AuthController::class, 'showRegisterForm'])->name('registrasi');
    Route::post('/registrasi', [AuthController::class, 'register'])->name('registrasi.post');
});

// Terautentikasi
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // DASHBOARD SUPER ADMIN 
    Route::middleware('role:super_admin')->prefix('superadmin')->group(function () {
        Route::view('/', 'superadmin.index')->name('superadmin.index');
        Route::view('/profil/tentang-kami', 'superadmin.profil-tentang')->name('profil.tentang');
        Route::view('/profil/sub-layanan', 'superadmin.profil-sub')->name('profil.sub');
        Route::view('/profil/team-infrastruktur', 'superadmin.profil-team')->name('profil.team');
        Route::view('/profil/link-layanan', 'superadmin.profil-link')->name('profil.link');
        Route::view('/data/VPS', 'superadmin.data-vps')->name('data.vps');
        Route::view('/data/WHM', 'superadmin.data-whm')->name('data.whm');
        Route::view('/metro/jaringan', 'superadmin.metro-jaringan')->name('metro.jaringan');
        Route::view('/device/software', 'superadmin.device-software')->name('device.software');
        Route::view('/device/hardware', 'superadmin.device-hardware')->name('device.hardware');
        Route::view('/serangan', 'superadmin.serangan')->name('serangan');
        Route::view('/gangguan', 'superadmin.gangguan')->name('gangguan');
        Route::view('/kategori-ticket', 'superadmin.kategori-ticket')->name('kategori.ticket');
        Route::view('/prioritas-ticket', 'superadmin.prioritas-ticket')->name('prioritas.ticket');
        Route::view('/daftar-ticket', 'superadmin.daftar-ticket')->name('daftar.ticket');
        Route::view('/perangkat', 'superadmin.perangkat')->name('perangkat');
        Route::view('/bidang', 'superadmin.bidang')->name('bidang');
        Route::view('/notifikasi', 'superadmin.notifikasi')->name('notifikasi');
        Route::view('/pengguna', 'superadmin.pengguna')->name('pengguna');
    });

    // DASHBOARD ADMIN PEMPROV (MONITORING & REPORTING)
    Route::middleware('role:admin_pemprov')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/', 'admin.index')->name('index');
        Route::view('/monitoring', 'admin.monitoring')->name('monitoring');
        Route::view('/kinerja', 'admin.kinerja')->name('kinerja');
        Route::view('/laporan', 'admin.laporan')->name('laporan');
    });

    // DASHBOARD HELPDESK UTAMA PERANGKAT DAERAH 
    Route::middleware('role:helpdesk_utama')->prefix('dinas')->name('dinas.')->group(function () {
        Route::view('/', 'dinas.index')->name('index');
        Route::view('/triage', 'dinas.triage')->name('triage');
        Route::view('/sop', 'dinas.sop')->name('sop');
    });


    // DASHBOARD HELPDESK BIDANG 
    Route::middleware(['auth', 'role:helpdesk_bidang'])->prefix('bidang')->name('bidang.')->group(function () {
        // Tiket & Penugasan Teknisi
        Route::get('/', [BidangController::class, 'index'])->name('index');
        Route::post('/tickets/{ticket}/assign', [BidangController::class, 'assignTeknisi'])->name('tickets.assign');

        // Manajemen Kategori, Layanan, SLA, dan Aktivitas
        Route::get('/kategori', [BidangController::class, 'kategori'])->name('kategori');
        Route::post('/kategori/category', [BidangController::class, 'storeCategory'])->name('kategori.category.store');
        Route::post('/kategori/service', [BidangController::class, 'storeService'])->name('kategori.service.store');
        Route::post('/kategori/services/{service}/sla', [BidangController::class, 'updateSla'])->name('kategori.updateSla');
        Route::post('/kategori/services/{service}/activity', [BidangController::class, 'storeActivity'])->name('kategori.storeActivity');

        // Forward & Riwayat
        Route::get('/forward', [ForwardController::class, 'index'])->name('forward');
        Route::post('/forward', [ForwardController::class, 'store'])->name('forward.store');
        Route::get('/history', [ForwardController::class, 'history'])->name('history');
    });

    // DASHBOARD PETUGAS HELPDESK / TEKNISI 
    Route::middleware('role:teknisi')->prefix('teknisi')->name('teknisi.')->group(function () {
        Route::view('/', 'teknisi.index')->name('index');
        Route::view('/history-teknisi', 'teknisi.history-teknisi')->name('history');
    });

    // DASHBOARD USER / PELAPOR
    Route::middleware('role:pelapor')->prefix('user')->name('user.')->group(function () {
        Route::view('/', 'user.index')->name('index');
        Route::view('/tiket', 'user.tiket')->name('tiket');
        Route::view('/chat', 'user.chat')->name('chat');
    });

    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
    //chat
    Route::middleware(['auth'])->group(function () {
        Route::get('/tickets/{ticket}/messages', [TicketMessageController::class, 'index'])->name('tickets.messages.index');
        Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])->name('tickets.messages.store');
    });
    //ticket
    Route::middleware(['auth', 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
        Route::get('/', [TeknisiController::class, 'index'])->name('index');
        Route::get('/tickets/{ticket}', [TeknisiController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/progress', [TeknisiController::class, 'updateProgress'])->name('tickets.progress');

        // Riwayat
        Route::get('/history-teknisi', [TeknisiController::class, 'historyIndex'])->name('history');
        Route::get('/history-teknisi/{ticket}', [TeknisiController::class, 'historyShow'])->name('history.show');
    });
});

require __DIR__.'/settings.php';