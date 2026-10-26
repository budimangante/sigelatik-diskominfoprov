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
    Schema::create('task_report_files', function(Blueprint $table){
        $table->foreignId('task_reports_id')->references('id')->on('task_reports')->cascadeOnDelete();
        $table->foreignId('file_id')->constrained()->cascadeOnDelete();
        $table->primary(['task_reports_id', 'file_id']);

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('task_report_files');
    }
};
