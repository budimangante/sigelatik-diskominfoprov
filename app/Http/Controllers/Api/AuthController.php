<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Login API untuk semua aktor (NIP untuk ASN/Teknisi, Email untuk Pelapor/Admin).
     */
    public function login(Request $request): JsonResponse
    {
        // Mendukung input berupa key 'login', 'nip', atau 'email'
        $loginInput = $request->input('login') 
            ?? $request->input('nip') 
            ?? $request->input('email');

        $password = $request->input('password');

        if (! $loginInput || ! $password) {
            return response()->json([
                'message' => 'Kredensial login dan password wajib diisi.'
            ], 422);
        }

        // Otomatis deteksi apakah input berupa format email atau NIP/username
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        $user = User::where($fieldType, $loginInput)->first();

        // Jika tidak ditemukan via NIP, coba cari via username
        if (! $user && $fieldType === 'nip') {
            $user = User::where('username', $loginInput)->first();
        }

        // Cek kecocokan password
        if (! $user || ! Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'NIP/Email atau password salah.'
            ], 401);
        }

        // Cek status keaktifan akun
        if (! $user->is_active) {
            return response()->json([
                'message' => 'Akun Anda sedang dinonaktifkan. Hubungi administrator.'
            ], 403);
        }

        // Buat Access Token via Laravel Passport
        $token = $user->createToken('SigelatikApiToken')->accessToken;

        $user->update(['last_login_at' => now()]);

        return response()->json([
            'message'      => 'Login berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load('roles'),
        ], 200);
    }

    /**
     * Registrasi Pelapor / Masyarakat via API.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'phone'             => 'nullable|string|max:20',
            'user_type'         => 'required|in:asn,pppk,pegawai_internal,unit_kerja_lain,masyarakat',
            'organization_name' => 'nullable|string|max:255',
            'nip'               => 'nullable|string|unique:users,nip',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $newUser = User::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'nip'          => $validated['nip'] ?? null,
                'password'     => Hash::make($validated['password']),
                'phone'        => $validated['phone'] ?? null,
                'user_type'    => $validated['user_type'],
                'organization' => $validated['organization_name'] ?? 'Masyarakat Umum',
                'is_active'    => true,
            ]);

            $pelaporRole = Roles::where('name', 'pelapor')->first();
            if ($pelaporRole) {
                $newUser->roles()->attach($pelaporRole->id);
            }

            return $newUser;
        });

        $token = $user->createToken('SigelatikApiToken')->accessToken;

        return response()->json([
            'message'      => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load('roles'),
        ], 201);
    }

    /**
     * Logout API (Mencabut/Revoke token Passport yang sedang aktif).
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json([
            'message' => 'Logout berhasil. Token telah dicabut.'
        ], 200);
    }
}