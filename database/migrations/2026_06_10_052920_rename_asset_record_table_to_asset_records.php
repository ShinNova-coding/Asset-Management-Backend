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
        Schema::table('asset_records', function (Blueprint $table) {
            Schema::rename('asset_record', 'asset_records');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_records', function (Blueprint $table) {
            Schema::rename('asset_records', 'asset_record');
        });
    }
};
