<?php

namespace App\Charts;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserVerificationChart
{
    public static function build(): array
    {
        $userModel = new User();
        $userTable = $userModel->getTable();
        $cols = Schema::getColumnListing($userTable);

        $totalUsers = User::count();
        $verifiedCount = 0;

        // 1. Prioritas Utama SIGELATIK: Cek apakah akun terdata di employments (SSO BKD / OPD)
        if (Schema::hasTable('employments')) {
            $verifiedCount = User::whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('employments')
                    ->whereColumn('employments.user_id', 'users.id');
            })->count();
        }
        // 2. Cek kolom status atau is_active jika ada
        elseif (in_array('is_active', $cols)) {
            $verifiedCount = User::where('is_active', 1)->count();
        } elseif (in_array('status', $cols)) {
            $verifiedCount = User::whereIn('status', ['active', 'verified', '1'])->count();
        } elseif (in_array('email_verified_at', $cols)) {
            $verifiedCount = User::whereNotNull('email_verified_at')->count();
        } else {
            // Fallback: Akun yang sudah memiliki peran/role di user_roles
            if (Schema::hasTable('user_roles')) {
                $verifiedCount = User::whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('user_roles')
                        ->whereColumn('user_roles.user_id', 'users.id');
                })->count();
            } else {
                $verifiedCount = $totalUsers;
            }
        }

        $unverifiedCount = max(0, $totalUsers - $verifiedCount);

        $verifiedPercent = $totalUsers > 0 ? round(($verifiedCount / $totalUsers) * 100) : 0;
        $unverifiedPercent = $totalUsers > 0 ? (100 - $verifiedPercent) : 0;

        return [
            'verified' => [
                'count'      => $verifiedCount,
                'percentage' => $verifiedPercent,
                'chart'      => [
                    'labels'   => ['Terverifikasi SSO', 'Belum Terverifikasi'],
                    'datasets' => [[
                        'data'            => [$verifiedCount, $unverifiedCount],
                        'backgroundColor' => ['#10b981', '#e2e8f0'],
                        'borderWidth'     => 0,
                    ]]
                ]
            ],
            'unverified' => [
                'count'      => $unverifiedCount,
                'percentage' => $unverifiedPercent,
                'chart'      => [
                    'labels'   => ['Belum Terverifikasi', 'Terverifikasi SSO'],
                    'datasets' => [[
                        'data'            => [$unverifiedCount, $verifiedCount],
                        'backgroundColor' => ['#ef4444', '#e2e8f0'],
                        'borderWidth'     => 0,
                    ]]
                ]
            ]
        ];
    }
}