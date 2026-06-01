<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_requests', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('users')->cascadeOnDelete();
            $table->string('asset_id');
            $table->foreign('asset_id')->references('asset_id')->on('assets')->cascadeOnDelete();
            $table->enum('status', ['requested', 'approved', 'rejected','returned'])
                ->default('requested');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};
