<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

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
            ],
            [
                'name'         => 'Ahmad Fauzi, S.Kom',
                'username'     => 'ahmadfauzi',
                'email'        => 'fauzi.bidang@pemprov.go.id',
                'nip'          => '198801152015031002',
                'phone'        => '081234567891',
                'user_type'    => 'asn',
                'organization' => 'Diskominfo Prov Kaltim',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'helpdesk_bidang',
            ],
            [
                'name'         => 'Rizky Pratama, A.Md',
                'username'     => 'rizkyteknisi',
                'email'        => 'rizky.teknisi@pemprov.go.id',
                'nip'          => '199507202020121005',
                'phone'        => '081234567892',
                'user_type'    => 'asn',
                'organization' => 'Diskominfo Prov Kaltim',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'teknisi',
            ],
            [
                'name'         => 'Siti Rahmawati, M.Si',
                'username'     => 'sitirahmawati',
                'email'        => 'siti.opd@pemprov.go.id',
                'nip'          => '198504102010012003',
                'phone'        => '081234567893',
                'user_type'    => 'asn',
                'organization' => 'Bappeda Prov Kaltim',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'helpdesk_utama',
            ],
            [
                'name'         => 'Administrator Pemprov',
                'username'     => 'adminpemprov',
                'email'        => 'admin@pemprov.go.id',
                'nip'          => '198001012005011001',
                'phone'        => '081234567894',
                'user_type'    => 'asn',
                'organization' => 'Setda Prov Kaltim',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'admin_pemprov',
            ],
            [
                'name'         => 'Super Administrator',
                'username'     => 'superadmin',
                'email'        => 'superadmin@pemprov.go.id',
                'nip'          => '197901012003011001',
                'phone'        => '081234567895',
                'user_type'    => 'asn',
                'organization' => 'Diskominfo Prov Kaltim',
                'password'     => $defaultPassword,
                'is_active'    => true,
                'role'         => 'asn',
                'target_role'  => 'super_admin',
            ],
        ];

        foreach ($users as $userData) {
            $targetRole = $userData['target_role'];
            unset($userData['target_role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Hubungkan user ke tabel pivot role
            $role = Roles::where('name', $targetRole)->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }
    }
}