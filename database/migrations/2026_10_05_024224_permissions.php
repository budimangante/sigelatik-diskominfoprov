<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    protected $primaryKey = 'permission_id';
    public $incrementing = false;
    protected $keyType = 'int';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function(Blueprint $table){
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
