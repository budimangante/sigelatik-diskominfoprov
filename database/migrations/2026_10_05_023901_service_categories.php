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
        Schema::create('service_categories',  function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('perangkat_daerah_id')
            ->references('id')
            ->on('perangkat_daerah')
            ->cascadeOnDelete();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
    }
            
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_categories');
    }
};
