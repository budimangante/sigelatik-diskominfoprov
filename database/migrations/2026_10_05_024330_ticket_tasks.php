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
        Schema::create('tickets_task', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
            ->references('id')
            ->on('tickets')
            ->cascadeOnDelete();

            // $table->foreignId('ticket_messages_id')
            // ->references('ticket_messages_id')
            // ->on('ticket_messages')
            // ->cascadeOnDelete();

            $table->foreignId('service_id')
            ->references('id')
            ->on('services')
            ->cascadeOnDelete();

            $table->foreignId('ticket_status_id')
            ->references('id')->on('ticket_statuses')
            ->restrictOnDelete();

            $table->foreignId('created_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();  

            $table->timestamp('received_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->string('close_type')->nullable();
            $table->string('updated_type')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_task');
    }
};