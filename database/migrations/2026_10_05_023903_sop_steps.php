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
        Schema::create('sops_steps', function (Blueprint $table){
            $table->id();
            $table->foreignId('sop_id')
            ->references('id')
            ->on('sops')
            ->cascadeOnDelete();
            $table->smallInteger('step_no');;
            $table->string('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sops_steps');
    }
};
