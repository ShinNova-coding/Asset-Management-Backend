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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id'); // must exist
            $table->foreign('employee_id')->references('employee_id')->on('users')->cascadeOnDelete();
            $table->string('asset_id');
            $table->foreign('asset_id')->references('asset_id')->on('assets')->cascadeOnDelete();
            $table->string('note')->nullable();
            $table->string('status')->default('available');
            $table->date('assign_date');//onboarding date
            $table->date('return_date');//offboarding date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
