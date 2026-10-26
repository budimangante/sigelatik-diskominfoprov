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
        // Menggunakan 'ticket_messages' (atau tetap 'ticket_massages' jika memang diharuskan)
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                  ->references('id')
                  ->on('tickets')
                  ->cascadeOnDelete();

            $table->foreignId('ticket_task_id')
                  ->nullable()
                  ->references('id')
                  ->on('tickets_task')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->text('body');
            $table->timestamp('read_at')->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};