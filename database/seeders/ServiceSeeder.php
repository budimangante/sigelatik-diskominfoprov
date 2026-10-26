<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Memastikan data sesuai dengan skema kolom MySQL (tipe integer, enum, not null, dll)
     */
    private function autoFill(string $table, array $data): array
    {
        $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
        $existingCols = [];

        foreach ($columns as $col) {
            $existingCols[$col->Field] = $col;
        }

        // 1. Buang kolom yang memang tidak ada di tabel target
        $data = array_intersect_key($data, $existingCols);

        // 2. Isi timestamps jika kolomnya ada
        if (isset($existingCols['created_at']) && !isset($data['created_at'])) {
            $data['created_at'] = now();
        }
        if (isset($existingCols['updated_at']) && !isset($data['updated_at'])) {
            $data['updated_at'] = now();
        }

        // 3. Inspeksi semua kolom dan sesuaikan tipe nilainya
        foreach ($existingCols as $name => $col) {
            if ($col->Extra === 'auto_increment') {
                continue;
            }

            $type = strtolower($col->Type);
            $isNumeric = str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float');
            $isEnum = str_starts_with($type, 'enum');

            // Ambil opsi valid pertama jika kolom bertipe enum
            $firstEnumOption = null;
            if ($isEnum && preg_match("/enum\('([^']+)'/", $col->Type, $matches)) {
                $firstEnumOption = $matches[1];
            }

            // Jika nilai sudah ada di array $data, pastikan tipe datanya cocok
            if (array_key_exists($name, $data)) {
                if ($isNumeric && !is_numeric($data[$name])) {
                    $data[$name] = 1;
                }
                continue;
            }

            // Jika kolom belum ada di $data dan berstatus NOT NULL tanpa default
            if ($col->Null === 'NO' && $col->Default === null) {
                if ($isNumeric) {
                    $data[$name] = 1;
                } elseif (str_contains($type, 'date') || str_contains($type, 'time')) {
                    $data[$name] = now();
                } elseif ($isEnum) {
                    $data[$name] = $firstEnumOption ?? 'dinas';
                } else {
                    $data[$name] = match ($name) {
                        'code', 'kode'              => 1,
                        'type', 'tipe'              => 'dinas',
                        'slug'                      => 'layanan-' . $table,
                        'name', 'nama'              => 'Dinas Komunikasi dan Informatika',
                        'description', 'keterangan' => 'Layanan operasional helpdesk',
                        default                     => 'Default',
                    };
                }
            }
        }

        return $data;
    }

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Data Induk: Perangkat Daerah
        $pdId = DB::table('perangkat_daerah')->value('id');
        if (!$pdId) {
            $pdData = $this->autoFill('perangkat_daerah', [
                'id'   => 1,
                'name' => 'Dinas Komunikasi dan Informatika',
                'code' => 1, // Integer aman untuk tipe int maupun varchar
            ]);
            DB::table('perangkat_daerah')->insert($pdData);
            $pdId = 1;
        }

        // 2. Data Induk: Bidang
        $bidangId = DB::table('bidang')->value('id');
        if (!$bidangId) {
            $bidangData = $this->autoFill('bidang', [
                'id'                  => 1,
                'perangkat_daerah_id' => $pdId,
                'name'                => 'Layanan Aplikasi',
                'code'                => 1,
            ]);
            DB::table('bidang')->insert($bidangData);
            $bidangId = 1;
        }

        // 3. Kategori Layanan (service_categories)
        $categories = [
            ['id' => 1, 'name' => 'Infrastruktur & Jaringan', 'code' => 1],
            ['id' => 2, 'name' => 'Sistem Informasi & Aplikasi', 'code' => 2],
            ['id' => 3, 'name' => 'Keamanan Informasi & Persandian', 'code' => 3],
        ];

        foreach ($categories as $cat) {
            $catData = $this->autoFill('service_categories', [
                'id'                  => $cat['id'],
                'name'                => $cat['name'],
                'code'                => $cat['code'],
                'perangkat_daerah_id' => $pdId,
                'bidang_id'           => $bidangId,
            ]);
            DB::table('service_categories')->updateOrInsert(['id' => $cat['id']], $catData);
        }

        // 4. Master Layanan (services)
        $services = [
            [
                'id'          => 1,
                'category_id' => 1,
                'name'        => 'Layanan Jaringan & Internet',
                'code'        => 1,
                'description' => 'Penanganan kendala jaringan LAN, WiFi, dan konektivitas internet OPD.',
            ],
            [
                'id'          => 2,
                'category_id' => 2,
                'name'        => 'Layanan Aplikasi & Sistem Informasi',
                'code'        => 2,
                'description' => 'Dukungan teknis aplikasi pemerintahan (SIMPEG, Srikandi, Email Dinas, dll).',
            ],
            [
                'id'          => 3,
                'category_id' => 3,
                'name'        => 'Layanan Keamanan Informasi & TTE',
                'code'        => 3,
                'description' => 'Penerbitan, pembaruan sertifikat TTE, dan audit keamanan sistem.',
            ],
        ];

        foreach ($services as $svc) {
            $svcData = $this->autoFill('services', [
                'id'                  => $svc['id'],
                'name'                => $svc['name'],
                'code'                => $svc['code'],
                'category_id'         => $svc['category_id'],
                'service_category_id' => $svc['category_id'],
                'description'         => $svc['description'],
                'perangkat_daerah_id' => $pdId,
                'bidang_id'           => $bidangId,
            ]);
            DB::table('services')->updateOrInsert(['id' => $svc['id']], $svcData);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}