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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
            ->references('id')
            ->on('service_categories')
            ->cascadeOnDelete();

            $table->foreignId('bidang_id')
            ->references('id')
            ->on('bidang')
            ->cascadeOnDelete();
            
            $table->string('name');
            $table->integer('code')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the 'services' table if it exists
        Schema::dropIfExists('services');
    }
};
