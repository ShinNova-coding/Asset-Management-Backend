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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('serial_number')->unique();
            $table->date('purchased_date');
            $table->integer('warranty_period');
            $table->string('model');
            $table->string('ram_capacity');
            $table->string('storage');
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('status')->default('available');
            $table->string('condition')->default('fair');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
