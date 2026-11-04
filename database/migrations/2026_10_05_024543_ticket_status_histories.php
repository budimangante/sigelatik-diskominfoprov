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
        Schema::create('ticket_status_histories', function(Blueprint $table){
            $table->id();
            $table->foreignId('ticket_id')->references('id')->on('tickets')->cascadeOnDelete();
            $table->foreignId('ticket_task_id')->nullable()->references('id')->on('tickets_task')->nullOnDelete();
            $table->foreignId('ticket_status_id')->references('id')->on('ticket_statuses')->cascadeOnDelete();
            $table->foreignId('pending_reasons_id')->nullable()->constrained('pending_reasons')->nullOnDelete();
            $table->string('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status_histories');
    }
};
