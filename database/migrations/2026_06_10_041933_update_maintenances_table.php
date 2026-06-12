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
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropColumn(['vendor', 'vendor_phno', 'vendor_address', 'cost', 'duration', 'payment']);

            $table->foreignUuid('accepted_by')->nullable()->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['accepted_by']);
            $table->dropColumn('accepted_by');

            $table->string('vendor')->nullable();
            $table->string('vendor_phno')->nullable();
             $table->string('vendor_address')->nullable();
            $table->integer('cost')->nullable();
            $table->integer('duration')->nullable();
            $table->string('payment')->nullable();
        });
    }
};
