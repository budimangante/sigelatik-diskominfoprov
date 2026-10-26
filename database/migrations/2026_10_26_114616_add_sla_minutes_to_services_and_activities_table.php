<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'sla_minutes')) {
                // Default 240 menit (4 jam)
                $table->unsignedInteger('sla_minutes')->default(240)->after('description');
            }
        });

        if (Schema::hasTable('service_activities')) {
            Schema::table('service_activities', function (Blueprint $table) {
                if (!Schema::hasColumn('service_activities', 'duration_minutes')) {
                    $table->unsignedInteger('duration_minutes')->default(60)->after('name');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'sla_minutes')) {
                $table->dropColumn('sla_minutes');
            }
        });

        if (Schema::hasTable('service_activities')) {
            Schema::table('service_activities', function (Blueprint $table) {
                if (Schema::hasColumn('service_activities', 'duration_minutes')) {
                    $table->dropColumn('duration_minutes');
                }
            });
        }
    }
};