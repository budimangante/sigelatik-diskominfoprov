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
        Schema::create('ticket_task_activities', function(Blueprint $table){
            $table->id();
            $table->foreignId('ticket_task_id')->references('id')->on('tickets_task');
            $table->foreignId('service_activities_id')->nullable()->references('id')->on('service_activities')->nullOnDelete();
            $table->string('name_snapshot');
            $table->smallInteger('sort_order');
            $table->integer('target_minutes_snapshot');
            $table->integer('paused_seconds');
            $table->boolean('is_sla_met')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_task_activities');
    }
};
