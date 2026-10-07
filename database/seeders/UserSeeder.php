<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'budi@gmail.com'],
            [
                'name'         => 'Budi Santoso',
                'username'     => null,
                'nip'          => null, // Masyarakat tidak punya NIP
                'organization' => 'Masyarakat Umum',
                'role'         => 'masyarakat',
                'password'     => Hash::make('password123'),
            ]
        );

        // 2. HELPDESK BIDANG (ASN - Pegawai Bidang TIK)
        User::updateOrCreate(
            ['nip' => '198801152015031002'],
            [
                'name'         => 'Ahmad Fauzi, S.Kom',
                'username'     => null,
                'email'        => 'fauzi.bidang@pemprov.go.id',
                'nip'          => '198801152015031002',
                'organization' => 'Bidang TIK Diskominfo',
                'role'         => 'helpdesk_bidang',
                'password'     => null, // Password aslinya diverifikasi via SSO BKD
            ]
        );

        // 3. HELPDESK TEKNISI (ASN - Tim Lapangan / Jaringan)
        User::updateOrCreate(
            ['nip' => '199507202020121005'],
            [
                'name'         => 'Rizky Pratama, A.Md',
                'username'     => null,
                'email'        => 'rizky.teknisi@pemprov.go.id',
                'nip'          => '199507202020121005',
                'organization' => 'Tim Teknis Infrastruktur & Jaringan',
                'role'         => 'helpdesk_teknisi',
                'password'     => null,
            ]
        );

        // 4. HELPDESK UTAMA (ASN - Perangkat Daerah / OPD)
        User::updateOrCreate(
            ['nip' => '198504102010012003'],
            [
                'name'         => 'Siti Rahmawati, M.Si',
                'username'     => null,
                'email'        => 'siti.opd@pemprov.go.id',
                'nip'          => '198504102010012003',
                'organization' => 'Dinas Pendidikan & Kebudayaan',
                'role'         => 'helpdesk_utama',
                'password'     => null,
            ]
        );

        // 5. ADMIN & SUPER ADMIN (Login via Username)
        User::updateOrCreate(
            ['username' => 'pemprov_admin'],
            [
                'name'         => 'Administrator Pemprov',
                'username'     => 'admin',
                'email'        => 'admin@pemprov.go.id',
                'nip'          => null,
                'organization' => 'Diskominfo Pemprov',
                'role'         => 'admin_default',
                'password'     => Hash::make('@dMinPemprov'),
            ]
        );

        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name'         => 'Super Administrator',
                'username'     => 'superadmin',
                'email'        => 'superadmin@pemprov.go.id',
                'nip'          => null,
                'organization' => 'Pusat Data & IT Pemprov',
                'role'         => 'super_admin',
                'password'     => Hash::make('@dMinPemprov'),
            ]
        );
    }
}
