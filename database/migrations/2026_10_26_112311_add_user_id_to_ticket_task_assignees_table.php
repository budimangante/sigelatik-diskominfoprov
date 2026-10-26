<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_task_assignees', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_task_assignees', 'user_id')) {
                $table->foreignId('user_id')
                      ->after('ticket_task_id')
                      ->constrained('users')
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_task_assignees', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_task_assignees', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};