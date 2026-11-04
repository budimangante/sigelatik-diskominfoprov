<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;
use App\Models\Employments;
use App\Models\PerangkatDaerah;
use App\Models\Bidang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. Master Data Perangkat Daerah (Kolom code menggunakan INTEGER)
        $opdDiskominfo = PerangkatDaerah::firstOrCreate(
            ['name' => 'Dinas Komunikasi dan Informatika'],
            ['code' => 1]
        );

        $opdBkd = PerangkatDaerah::firstOrCreate(
            ['name' => 'Badan Kepegawaian Daerah'],
            ['code' => 2]
        );

        $opdSetda = PerangkatDaerah::firstOrCreate(
            ['name' => 'Sekretariat Daerah'],
            ['code' => 3]
        );

        // 2. Master Data Bidang (Cek kolom code secara aman)
        $bidangAptikaPayload = [
            'perangkat_daerah_id' => $opdDiskominfo->id,
            'name'                => 'Aplikasi Informatika (APTIKA)'
        ];
        if (Schema::hasColumn('bidang', 'code')) {
            $bidangAptikaPayload['code'] = 1;
        }
        $bidangAptika = Bidang::firstOrCreate(
            ['perangkat_daerah_id' => $opdDiskominfo->id, 'name' => 'Aplikasi Informatika (APTIKA)'],
            $bidangAptikaPayload
        );

        $bidangBkdPayload = [
            'perangkat_daerah_id' => $opdBkd->id,
            'name'                => 'Pengembangan Pegawai'
        ];
        if (Schema::hasColumn('bidang', 'code')) {
            $bidangBkdPayload['code'] = 2;
        }
        $bidangBkd = Bidang::firstOrCreate(
            ['perangkat_daerah_id' => $opdBkd->id, 'name' => 'Pengembangan Pegawai'],
            $bidangBkdPayload
        );

        $bidangSetdaPayload = [
            'perangkat_daerah_id' => $opdSetda->id,
            'name'                => 'Biro Organisasi'
        ];
        if (Schema::hasColumn('bidang', 'code')) {
            $bidangSetdaPayload['code'] = 3;
        }
        $bidangSetda = Bidang::firstOrCreate(
            ['perangkat_daerah_id' => $opdSetda->id, 'name' => 'Biro Organisasi'],
            $bidangSetdaPayload
        );

        $users = [
            [
                'name'         => 'Budi Santoso',
                'username'     => 'budisantoso',
                'email'        => 'budi@gmail.com',
                'nip'          => null,
                'phone'        => '081234567890',
                'user_type'    => 'masyarakat',
                'organization' => 'Masyarakat Umum',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'masyarakat',
                'target_role'  => 'pelapor',
                'opd_id'       => null,
                'bidang_id'    => null,
                'jabatan'      => null,
            ],
            [
                'name'         => 'Ahmad Fauzi, S.Kom',
                'username'     => 'ahmadfauzi',
                'email'        => 'fauzi.bidang@pemprov.go.id',
                'nip'          => '198801152015031002',
                'phone'        => '081234567891',
                'user_type'    => 'asn',
                'organization' => 'Dinas Komunikasi dan Informatika',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'helpdesk_bidang',
                'opd_id'       => $opdDiskominfo->id,
                'bidang_id'    => $bidangAptika->id,
                'jabatan'      => 'Pranata Komputer Ahli Muda',
            ],
            [
                'name'         => 'Rizky Pratama, A.Md',
                'username'     => 'rizkyteknisi',
                'email'        => 'rizky.teknisi@pemprov.go.id',
                'nip'          => '199507202020121005',
                'phone'        => '081234567892',
                'user_type'    => 'asn',
                'organization' => 'Dinas Komunikasi dan Informatika',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'teknisi',
                'opd_id'       => $opdDiskominfo->id,
                'bidang_id'    => $bidangAptika->id,
                'jabatan'      => 'Teknisi Jaringan & Infrastruktur',
            ],
            [
                'name'         => 'Siti Rahmawati, M.Si',
                'username'     => 'sitirahmawati',
                'email'        => 'siti.opd@pemprov.go.id',
                'nip'          => '198504102010012003',
                'phone'        => '081234567893',
                'user_type'    => 'asn',
                'organization' => 'Badan Kepegawaian Daerah',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'helpdesk_utama',
                'opd_id'       => $opdBkd->id,
                'bidang_id'    => $bidangBkd->id,
                'jabatan'      => 'Pengelola Sistem Informasi Kepegawaian',
            ],
            [
                'name'         => 'Administrator Pemprov',
                'username'     => 'adminpemprov',
                'email'        => 'admin@pemprov.go.id',
                'nip'          => '198001012005011001',
                'phone'        => '081234567894',
                'user_type'    => 'asn',
                'organization' => 'Sekretariat Daerah',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'admin_pemprov',
                'opd_id'       => $opdSetda->id,
                'bidang_id'    => $bidangSetda->id,
                'jabatan'      => 'Analis Kebijakan Ahli Muda',
            ],
            [
                'name'         => 'Super Administrator',
                'username'     => 'superadmin',
                'email'        => 'superadmin@pemprov.go.id',
                'nip'          => '197901012003011001',
                'phone'        => '081234567895',
                'user_type'    => 'asn',
                'organization' => 'Dinas Komunikasi dan Informatika',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'super_admin',
                'opd_id'       => $opdDiskominfo->id,
                'bidang_id'    => $bidangAptika->id,
                'jabatan'      => 'Kepala Seksi Operasional Sistem',
            ],
        ];

        foreach ($users as $userData) {
            $targetRole = $userData['target_role'];
            $nip        = $userData['nip'] ?? null;
            $opdId      = $userData['opd_id'];
            $bidangId   = $userData['bidang_id'];
            $jabatan    = $userData['jabatan'] ?? 'Staf ASN';

            unset($userData['target_role'], $userData['opd_id'], $userData['bidang_id'], $userData['jabatan']);

            // 1. Simpan Akun User
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // 2. Hubungkan ke Role
            $role = Roles::where('name', $targetRole)->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            // 3. Simpan Seluruh Kolom Wajib ke Tabel Employments
            if ($nip && Schema::hasTable('employments')) {
                Employments::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'perangkat_daerah_id' => $opdId ?? $opdDiskominfo->id,
                        'bidang_id'           => $bidangId ?? $bidangAptika->id,
                        'nip'                 => $nip,
                        'name'                => $userData['name'],
                        'position_title'      => $jabatan,
                        'started_at'          => '2024-01-01',
                        'source'              => 'internal',
                        'current_flag'        => 1,
                    ]
                );
            }
        }
    }
}