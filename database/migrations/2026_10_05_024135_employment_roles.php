<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('employment_roles', function (Blueprint $table) 
        {

            $table->foreignId('employment_id')
                ->references('id')
                ->on('employments')
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->references('id') 
                ->on('roles')
                ->cascadeOnDelete();
                
            $table->primary(['employment_id', 'role_id']);
            $table->timestamp('created_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_roles');
    }
};
