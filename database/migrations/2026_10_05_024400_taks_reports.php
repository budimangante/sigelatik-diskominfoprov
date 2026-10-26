<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_task_id')
                  ->references('id')
                  ->on('tickets_task')
                  ->cascadeOnDelete();

            $table->foreignId('name')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->enum('visibility', ['public', 'internal'])->default('public');

            $table->text('body');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_reports');
    }
};