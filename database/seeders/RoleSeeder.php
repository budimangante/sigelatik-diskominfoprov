<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roles;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'code'        => 'super_admin',
                'name'        => 'super_admin',
                'description' => 'Super Administrator Sistem',
            ],
            [
                'code'        => 'admin_pemprov',
                'name'        => 'admin_pemprov',
                'description' => 'Admin Eksekutif Monitoring Pemprov',
            ],
            [
                'code'        => 'helpdesk_utama',
                'name'        => 'helpdesk_utama',
                'description' => 'Helpdesk Utama / Triage Dinas OPD',
            ],
            [
                'code'        => 'helpdesk_bidang',
                'name'        => 'helpdesk_bidang',
                'description' => 'Helpdesk Penerima Bidang Teknis',
            ],
            [
                'code'        => 'teknisi',
                'name'        => 'teknisi',
                'description' => 'Petugas Teknis Lapangan',
            ],
            [
                'code'        => 'pelapor',
                'name'        => 'pelapor',
                'description' => 'Pelapor Umum / ASN / PPPK / Masyarakat',
            ],
        ];

        foreach ($roles as $role) {
            Roles::firstOrCreate(['code' => $role['code']], $role);
        }
    }
}