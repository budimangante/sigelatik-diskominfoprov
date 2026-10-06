<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'         => 'Admin Pemprov Default',
                'password'     => Hash::make('@dMinPemprov'),
                'role'         => 'admin_default',
                'organization' => 'Pemerintah Provinsi',
            ]
        );
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name'         => 'Super Admin',
                'password'     => Hash::make('@dMinPemprov'),
                'role'         => 'super_admin',
                'organization' => 'Pemerintah Provinsi',
            ]
        );
    }
}