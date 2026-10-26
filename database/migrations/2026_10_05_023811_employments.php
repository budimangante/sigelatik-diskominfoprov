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
        Schema::create('employments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('perangkat_daerah_id')
                  ->references('id')
                  ->on('perangkat_daerah')
                  ->cascadeOnDelete();

            $table->foreignId('bidang_id')
                ->references('id')
                ->on('bidang')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('position_title');
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->string('end_reason')->nullable();
            $table->enum('source', ['internal', 'external'])->nullable();
            $table->smallInteger('current_flag')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};