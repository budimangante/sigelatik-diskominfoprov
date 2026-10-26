<?php

namespace App\Http\Controllers\Web;

use App\Events\UserAuthNotification;
use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('login');
        $password   = $request->input('password');
        $fieldType  = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        $user = User::where($fieldType, $loginInput)->first();

        // 1. Kondisi Gagal Login (Password atau Akun Salah)
        if (! $user || ! Hash::check($password, $user->password)) {
            broadcast(new UserAuthNotification('error', "Percobaan login gagal untuk akun: {$loginInput}"));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIP/Email atau kata sandi salah.',
                ], 422);
            }

            return back()->withErrors([
                'login' => 'NIP/Email atau kata sandi salah.',
            ])->onlyInput('login');
        }

        // 2. Kondisi Akun Dinonaktifkan
        if (! $user->is_active) {
            broadcast(new UserAuthNotification('error', "Login ditolak: Akun {$user->name} dinonaktifkan."));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda sedang dinonaktifkan. Hubungi administrator.',
                ], 403);
            }

            return back()->withErrors([
                'login' => 'Akun Anda sedang dinonaktifkan. Hubungi administrator.',
            ]);
        }

        // 3. Proses Login Berhasil
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        // Siarkan ke Reverb (untuk tab monitoring/admin lain)
        broadcast(new UserAuthNotification('success', "{$user->name} berhasil masuk ke sistem.", $user->name));

        // Flash message untuk ditampilkan sebagai toast di dashboard pengguna yang bersangkutan
        session()->flash('welcome', "Selamat datang kembali, {$user->name}!");

        $redirectUrl = $this->getRedirectUrlByRole($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    public function showRegisterForm(): View
    {
        return view('auth.registrasi');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'phone'             => 'nullable|string|max:20',
            'user_type'         => 'nullable|string',
            'organization_name' => 'nullable|string|max:255',
            'organization'      => 'nullable|string|max:255',
            'nip'               => 'nullable|string|unique:users,nip',
        ], [
            'email.unique'       => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal harus 8 karakter.',
        ]);

        // Tangkap user baru ke variabel $newUser
        $newUser = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'nip'          => $validated['nip'] ?? null,
                'password'     => Hash::make($validated['password']),
                'phone'        => $validated['phone'] ?? null,
                'user_type'    => $validated['user_type'] ?? 'masyarakat',
                'organization' => $validated['organization_name'] ?? $validated['organization'] ?? 'Masyarakat Umum',
                'is_active'    => true,
                'role'         => 'masyarakat',
            ]);

            $pelaporRole = Roles::where('name', 'pelapor')->first();
            if ($pelaporRole) {
                $user->roles()->attach($pelaporRole->id);
            }

            return $user;
        });

        // Siarkan event ke Reverb secara instan bahwa ada pendaftar baru
        broadcast(new UserAuthNotification(
            'success',
            "Pendaftaran Akun Baru: {$newUser->name} ({$newUser->organization})",
            $newUser->name
        ));

        // Kembalikan ke halaman login dengan flash toast
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan masuk dengan akun Anda.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }

    protected function getRedirectUrlByRole(User $user): string
    {
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
                return route($routeName);
            }
        }

        return route('beranda');
    }

    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return redirect()->intended($this->getRedirectUrlByRole($user));
    }
}