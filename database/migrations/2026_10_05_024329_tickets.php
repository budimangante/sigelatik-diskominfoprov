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
        Schema::create('tickets', function (Blueprint $table)
        {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('public_token')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employment_id')
                ->nullable()
                ->references('id')
                ->on('employments')
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->references('id')
                ->on('services')
                ->cascadeOnDelete();

            $table->foreignId('ticket_status_id')
                ->references('id')
                ->on('ticket_statuses')
                ->restrictOnDelete();

            $table->string('subject');
            $table->text('description');

            $table->timestamp('solved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable(); 

            $table->timestamps(); 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
