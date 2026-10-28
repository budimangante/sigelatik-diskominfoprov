<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\LacakTicketController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\TicketMessageController;
use App\Http\Controllers\Web\TeknisiController;
use App\Http\Controllers\Web\ForwardController;
use App\Http\Controllers\Web\BidangController;


Route::get('/{section?}', function ($section = null) {
    return view('beranda.index-beranda', ['activeSection' => $section]);
})->where('section', 'tentang|statistik|layanan')->name('beranda');

Route::match(['get', 'post'], '/lacak-tiket', [LacakTicketController::class, 'track'])->name('ticket.track');

Route::get('/home', fn () => redirect()->route('beranda'))->name('home');
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

    Route::middleware('role:admin_pemprov')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/', 'admin.index')->name('index');
        Route::view('/monitoring', 'admin.monitoring')->name('monitoring');
        Route::view('/kinerja', 'admin.kinerja')->name('kinerja');
        Route::view('/laporan', 'admin.laporan')->name('laporan');
    });
 
    Route::middleware('role:helpdesk_utama')->prefix('dinas')->name('dinas.')->group(function () {
        Route::view('/', 'dinas.index')->name('index');
    });


    Route::middleware(['auth', 'role:helpdesk_bidang'])->prefix('bidang')->name('bidang.')->group(function () {
        Route::get('/', [BidangController::class, 'index'])->name('index');
        Route::post('/tickets/{ticket}/assign', [BidangController::class, 'assignTeknisi'])->name('tickets.assign');
        
        Route::get('/kategori', [BidangController::class, 'kategori'])->name('kategori');
        Route::post('/kategori', [BidangController::class, 'storeCategory'])->name('kategori.store');
        Route::post('/layanan', [BidangController::class, 'storeService'])->name('service.store');
        Route::patch('/layanan/{service}/sla', [BidangController::class, 'updateSla'])->name('service.updateSla');
        
        Route::get('/forward', [ForwardController::class, 'index'])->name('forward');
        Route::post('/forward', [ForwardController::class, 'store'])->name('forward.store');
        Route::get('/history', [ForwardController::class, 'history'])->name('history');
        

        Route::get('/sop', [BidangController::class, 'sopIndex'])->name('sop');
        Route::post('/sop', [BidangController::class, 'storeSop'])->name('sop.store');
        Route::post('/sop/{id}/update', [BidangController::class, 'updateSop'])->name('sop.update');
        Route::post('/sop/{id}/step', [BidangController::class, 'storeSopStep'])->name('sop.step');
        Route::get('/sop/{slug}/view', [BidangController::class, 'viewPdf'])->name('sop.view');
        Route::post('/sop/step/{stepId}/update', [BidangController::class, 'updateSopStep'])->name('sop.step.update');
        Route::delete('/sop/step/{stepId}', [BidangController::class, 'destroySopStep'])->name('sop.step.destroy');
        
        Route::get('/triage', [BidangController::class, 'triageIndex'])->name('triage');
        Route::post('/triage/{ticket}/process', [BidangController::class, 'processTriage'])->name('triage.process');
        Route::post('/triage/{ticket}/pending', [BidangController::class, 'pendingTriage'])->name('triage.pending');
        Route::post('/triage/{ticket}/reassign', [BidangController::class, 'reassignTeknisi'])->name('triage.reassign');
    });

    Route::middleware('role:teknisi')->prefix('teknisi')->name('teknisi.')->group(function () {
        Route::view('/', 'teknisi.index')->name('index');
        Route::view('/history-teknisi', 'teknisi.history-teknisi')->name('history');
        Route::post('/tickets/{ticket}/start', [TeknisiController::class, 'startWork'])->name('tickets.start');
    });


    Route::middleware(['auth', 'role:pelapor'])->prefix('user')->name('user.')->group(function () {
        Route::view('/', 'user.index')->name('index');
        Route::get('/tiket', [UserController::class, 'myTickets'])->name('tiket');
        Route::get('/tickets', [UserController::class, 'myTickets'])->name('tickets.index');
        Route::get('/tiket/buat', [UserController::class, 'createTicket'])->name('tickets.create');
        Route::post('/tiket/simpan', [UserController::class, 'storeTicket'])->name('tickets.store');
        Route::get('/chat', [UserController::class, 'chat'])->name('chat');
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