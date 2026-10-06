<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'organization' => 'nullable|string|max:255',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'organization' => $validated['organization'] ?? 'Masyarakat Umum',
            'password'     => Hash::make($validated['password']),
            'role'         => 'masyarakat',
        ]);

        Auth::login($user);

        $token = $user->createToken('auth_token', ['role:masyarakat'])->plainTextToken;

        return response()->json([
            'message'      => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $identifier = $request->input('identifier') 
            ?? $request->input('nip') 
            ?? $request->input('email') 
            ?? $request->input('username');

        if (!$identifier || !$request->filled('password')) {
            return response()->json([
                'message' => 'NIP, Email, atau Username beserta Password wajib disertakan.'
            ], 422);
        }

        $identifier = trim((string) $identifier);
        $password   = (string) $request->input('password');

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->loginEmail($identifier, $password);
        }

        if (ctype_digit($identifier)) {
            return $this->loginSsoBKD($identifier, $password);
        }
        return $this->loginAdmin($identifier, $password);
    }
    /**
     * Logika SSO ke Server Dinas BKD.
     * DI SINI tempat kode SSO dan try-catch berada.
     */
    private function loginSsoBKD(string $nip, string $password): JsonResponse
    {
        // Ambil URL SSO dari config atau fallback ke default
        $ssoApiUrl = config('services.bkd.sso_url', 'https://sso.bkd.go.id/api/login');

        try {
            // Tembak API SSO eksternal
            $response = Http::withHeaders([
                'Accept'    => 'application/json',
                'X-API-KEY' => config('services.bkd.client_key'),
            ])->timeout(10)->post($ssoApiUrl, [
                'nip'      => $nip,
                'password' => $password,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Server SSO Dinas sedang tidak dapat dihubungi. Silakan coba beberapa saat lagi.',
            ], 503);
        }

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'nip' => ['NIP atau password SSO tidak valid.'],
            ]);
        }

        $ssoData = $response->json('data') ?? $response->json();

        $roleMap = [
            'superadmin'  => 'super_admin',
            'opd'         => 'helpdesk_utama',
            'bidang'      => 'helpdesk_bidang',
            'teknisi'     => 'helpdesk_teknisi',
        ];

        $bkdRole     = $ssoData['role_bkd'] ?? $ssoData['role'] ?? '';
        $resolvedRole = $roleMap[$bkdRole] ?? 'helpdesk_teknisi';

        $user = User::updateOrCreate(
            ['nip' => $nip],
            [
                'name'         => $ssoData['nama'] ?? $ssoData['name'] ?? 'Pegawai BKD',
                'organization' => $ssoData['instansi'] ?? $ssoData['opd'] ?? 'Perangkat Daerah',
                'role'         => $resolvedRole,
                'password'     => null, // Password dikelola SSO, tidak disimpan lokal
            ]
        );
        Auth::login($user);
        $token = $user->createToken('sso_token', ["role:{$user->role}"])->plainTextToken;

        return response()->json([
            'message'      => 'Login SSO berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    private function loginEmail(string $email, string $password): JsonResponse
    {
        $user = User::where('email', $email)->where('role', 'masyarakat')->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }
        Auth::login($user);
        $token = $user->createToken('auth_token', ['role:masyarakat'])->plainTextToken;

        return response()->json([
            'message'      => 'Login berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }
    
    private function loginAdmin(string $username, string $password): JsonResponse
    {
        $user = User::where('username', $username)
            ->whereIn('role', ['admin_default', 'super_admin'])
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password admin salah.'],
            ]);
        }
        Auth::login($user);

        $isSuperAdmin = $user->role === 'super_admin';
        $tokenName    = $isSuperAdmin ? 'superadmin_token' : 'admin_token';
        $message      = $isSuperAdmin ? 'Login super admin berhasil' : 'Login admin berhasil';

        $token = $user->createToken($tokenName, ["role:{$user->role}"])->plainTextToken;

        return response()->json([
            'message'      => $message,
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // $request->user()->currentAccessToken()->delete();
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}