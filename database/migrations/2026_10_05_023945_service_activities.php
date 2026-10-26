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
        Schema::create('service_activities', function(Blueprint $table)
        {
            $table->id();
            $table->foreignId('service_id')->references('id')->on('services')->cascadeOnDelete();
            $table->string('name');
            $table->integer('targer_minutes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_acivities');
    }
};
