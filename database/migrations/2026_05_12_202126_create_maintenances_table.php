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
            $table->id();
            $table->string('asset_id');
            $table->foreign('asset_id')->references('asset_id')->on('assets')->cascadeOnDelete();
            $table->string('employee_id'); 
            $table->foreign('employee_id')->references('employee_id')->on('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('issue_type');
            $table->text('problem_description');
            $table->string('vendor')->nullable();
            $table->string('vendor_phno')->nullable();
             $table->string('vendor_address')->nullable();
            $table->integer('cost')->nullable();
            $table->string('remark')->nullable();
            $table->integer('duration')->nullable();
            $table->string('payment')->nullable();
            $table->string('status')->default('pending');
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
