<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Event\Telemetry\Duration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assets_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignUuid('users_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('categories_id')->constrained('categories')->cascadeOnDelete();
            $table->string('issue_type');
            $table->text('problem_description');
            $table->string('vendor')->nullable();
            $table->string('vendor_phno')->nullable();
             $table->string('vendor_address')->nullable();
            $table->integer('cost')->nullable();
            $table->string('remark')->nullable();
            $table->integer('duration')->nullable();
            $table->string('payment')->nullable();
            $table->string('status')->default('requested');
            $table->date('maintenance_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
