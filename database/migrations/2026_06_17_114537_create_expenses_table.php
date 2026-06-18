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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignUuid('users_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('maintenances_id')->nullable()->constrained('maintenances')->cascadeOnDelete();
            $table->foreignUuid('assets_id')->nullable()->constrained('assets')->cascadeOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('expense_type');
            $table->integer('cost');
            $table->string('status')->default('requested');
            $table->string('remark')->nullable();
            $table->string('description')->nullable();
            $table->date('expense_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
