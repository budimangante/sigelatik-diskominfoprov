<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\Employments;
use App\Models\PerangkatDaerah;
use App\Models\Roles;
use App\Models\User;
use App\Models\Bidang;
class PenggunaController extends Controller
{
        public function penggunaIndex(Request $request): View
    {
        $search = $request->query('search');
        $roleFilter = $request->query('role');

        $roles = Roles::orderBy('name', 'asc')->get();

        // 100% Eloquent Eager Loading relasi bertingkat (Nested Eager Loading)
        $users = User::with([
                'roles',
                'employments.perangkatDaerah',
                'employments.bidang'
            ])
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('employments', function ($emp) use ($search) {
                            $emp->where('nip', 'like', "%{$search}%");
                        });
                });
            })
            ->when($roleFilter, function ($q, $roleFilter) {
                $q->whereHas('roles', function ($r) use ($roleFilter) {
                    $r->where('roles.id', $roleFilter);
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // Transformasi nilai untuk kemudahan render di Blade
        foreach ($users as $u) {
            $currentEmp = $u->employments->first();
            $u->nip = $currentEmp?->nip ?? '-';
            $u->opd_name = $currentEmp?->perangkatDaerah?->name ?? 'Pemerintah Provinsi';
        }

        return view('superadmin.pengguna', compact('users', 'roles', 'search', 'roleFilter'));
    }

    /**
     * AJAX: Verifikasi NIP Pegawai via Eloquent Model
     */
    public function cekNipBkd(Request $request): JsonResponse
    {
        $nip = trim($request->query('nip', ''));

        if (empty($nip) || strlen($nip) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Format NIP tidak valid (minimal 8 karakter).'
            ], 422);
        }

        // Cek akun aktif via Eloquent
        $existingUser = Employments::where('nip', $nip)
            ->whereNotNull('user_id')
            ->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'NIP ini sudah terdaftar sebagai akun pengguna aktif di SIGELATIK.'
            ], 409);
        }

        // Cari data profil kepegawaian dengan Eager Loading Perangkat Daerah
        $pegawaiBkd = Employments::with('perangkatDaerah')->where('nip', $nip)->first();
        $defaultOpd = PerangkatDaerah::first();

        return response()->json([
            'success' => true,
            'data' => [
                'nip'                 => $nip,
                'name'                => $pegawaiBkd?->name ?? ('Pegawai ASN ' . substr($nip, 0, 8)),
                'email'               => $nip . '@kaltimprov.go.id',
                'opd_name'            => $pegawaiBkd?->perangkatDaerah?->name ?? $defaultOpd?->name ?? 'Dinas Komunikasi dan Informatika',
                'perangkat_daerah_id' => $pegawaiBkd?->perangkat_daerah_id ?? $defaultOpd?->id ?? 1,
                'source'              => $pegawaiBkd ? 'Database Terverifikasi BKD' : 'Direktori Kepegawaian Pemprov'
            ]
        ]);
    }

    /**
     * Daftarkan Pegawai & Tetapkan Role via Eloquent
     */
    public function storePengguna(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip'                 => 'required|string|max:30',
            'name'                => 'required|string|max:255',
            'email'               => 'required|string|email|max:255|unique:users,email',
            'role_id'             => 'required|exists:roles,id',
            'perangkat_daerah_id' => 'nullable|integer|exists:perangkat_daerah,id',
        ], [
            'nip.required'     => 'NIP pegawai wajib diisi.',
            'name.required'    => 'Nama ASN terverifikasi tidak boleh kosong.',
            'email.required'   => 'Email dinas tidak boleh kosong.',
            'email.unique'     => 'Email atau NIP ini sudah memiliki akun aktif.',
            'role_id.required' => 'Pilih peran/role untuk pegawai ini.',
        ]);

        // 1. Simpan Akun User
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make(Str::random(32)),
        ]);

        // 2. Pasang Relasi BelongsToMany Role
        $user->roles()->sync([$validated['role_id']]);

        // 3. Cari OPD & Bidang Terkait secara Eloquent
        $opdId = $validated['perangkat_daerah_id'] ?? PerangkatDaerah::first()?->id ?? 1;
        $bidang = Bidang::where('perangkat_daerah_id', $opdId)->first() ?? Bidang::first();

        // 4. Simpan Data Kepegawaian
        Employments::updateOrCreate(
            ['nip' => $validated['nip']],
            [
                'user_id'             => $user->id,
                'perangkat_daerah_id' => $opdId,
                'bidang_id'           => $bidang?->id ?? 1,
                'name'                => $validated['name'],
                'position_title'      => 'Staf ASN',
                'started_at'          => now()->toDateString(),
                'source'              => 'internal',
                'current_flag'        => 1,
            ]
        );

        return redirect()->route('pengguna')->with('success', "Akun pegawai NIP {$validated['nip']} berhasil didaftarkan.");
    }

    /**
     * Ubah Role Pengguna via Eloquent
     */
    public function updatePengguna(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ], [
            'role_id.required' => 'Pilih role baru untuk pengguna.',
        ]);

        $user->roles()->sync([$validated['role_id']]);

        return redirect()->route('pengguna')->with('success', "Role untuk {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus Pengguna via Eloquent
     */
    public function destroyPengguna(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Super Admin yang sedang aktif digunakan.']);
        }

        // Lepas relasi employments
        if ($user->employments()->exists()) {
            $user->employments()->delete();
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('pengguna')->with('success', "Pengguna {$user->name} berhasil dihapus.");
    }

}
