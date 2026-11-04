<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Charts\TicketMonthlyChart;
use App\Charts\AverageSlaChart;
use App\Charts\TicketCategoryChart;
use App\Charts\TicketStatusChart;
use App\Charts\UserVerificationChart;
use App\Models\Bidang;
use App\Models\PerangkatDaerah;
use App\Models\Roles;
use App\Models\Tickets;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class SuperAdminController extends Controller
{
    /**
     * Dashboard Overview (Ringkasan Metrik & Grafik SLA)
     */
    public function index(): View
    {
        $totalTickets = Tickets::count();
        $thisMonthTickets = Tickets::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Eloquent Relationship Checks
        $activeOpdCount = Bidang::whereHas('perangkatDaerah')->count();
        $totalBidang = Bidang::count();

        $totalPegawai = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%pegawai%')->orWhere('name', 'like', '%user%');
        })->count();

        $teknisiCount = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%teknisi%');
        })->count();

        $criticalTickets = Tickets::where('ticket_status_id', 1)->count();

        $roles = Roles::orderBy('name', 'asc')->get();

        $chartData = [
            'ticketMonthly'    => TicketMonthlyChart::build(),
            'averageSla'       => AverageSlaChart::build(),
            'ticketCategory'   => TicketCategoryChart::build(),
            'ticketStatus'     => TicketStatusChart::build(),
            'userVerification' => UserVerificationChart::build(),
        ];

        return view('superadmin.index', compact(
            'totalTickets',
            'thisMonthTickets',
            'activeOpdCount',
            'totalBidang',
            'roles',
            'totalPegawai',
            'teknisiCount',
            'criticalTickets',
            'chartData'
        ));
    }

    /**
     * Tampilkan Pengguna dengan Eager Loading Relasi Roles & Kepegawaian
     */

    public function bidangIndex(Request $request): View
    {
        $search = $request->query('search');
        $opdFilter = $request->query('perangkat_daerah_id');

        $perangkatDaerahList = PerangkatDaerah::orderBy('name', 'asc')->get();

        // Eloquent Eager Loading relasi induk + Eager Count relasi pegawai
        $bidangList = Bidang::with(['perangkatDaerah'])
            ->withCount('employments')
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($opdFilter, function ($q, $opdFilter) {
                $q->where('perangkat_daerah_id', $opdFilter);
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.bidang', compact('bidangList', 'perangkatDaerahList', 'search', 'opdFilter'));
    }

    /**
     * Tambah Bidang Baru via Eloquent
     */
    public function storeBidang(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'code'                => 'nullable|string|max:50',
        ], [
            'name.required'                => 'Nama bidang wajib diisi.',
            'perangkat_daerah_id.required' => 'Pilih Perangkat Daerah induk.',
            'perangkat_daerah_id.exists'   => 'Perangkat Daerah tidak valid.',
            'code.max'                     => 'Kode bidang maksimal 50 karakter.',
        ]);

        Bidang::create($validated);

        return redirect()->route('bidang')->with('success', 'Bidang baru berhasil ditambahkan.');
    }  

    /**
     * Ubah Data Bidang via Eloquent
     */
    public function updateBidang(Request $request, Bidang $bidang): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'code'                => 'nullable|string|max:50',
        ], [
            'name.required'                => 'Nama bidang wajib diisi.',
            'perangkat_daerah_id.required' => 'Pilih Perangkat Daerah induk.',
            'code.max'                     => 'Kode bidang maksimal 50 karakter.',
        ]);

        $bidang->update($validated);

        return redirect()->route('bidang')->with('success', "Data bidang {$bidang->name} berhasil diperbarui.");
    }

    /**
     * Hapus Bidang via Eloquent
     */
    public function destroyBidang(Bidang $bidang): RedirectResponse
    {
        if ($bidang->employments()->exists()) {
            return back()->withErrors(['error' => "Bidang {$bidang->name} tidak dapat dihapus karena masih digunakan oleh data kepegawaian ASN."]);
        }

        $namaBidang = $bidang->name;
        $bidang->delete();

        return redirect()->route('bidang')->with('success', "Bidang {$namaBidang} berhasil dihapus.");
    }

}