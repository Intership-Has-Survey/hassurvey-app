<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penjualan
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('sumber')->nullable();
        });

        // Sewa
        Schema::table('sewa', function (Blueprint $table) {
            $table->string('sumber')->nullable();
        });

        // Kalibrasi
        Schema::table('kalibrasis', function (Blueprint $table) {
            $table->uuid('sales_id')->nullable();
            $table->string('sumber')->nullable();

            // Kalau mau ada relasi ke tabel sales:
            // $table->foreign('sales_id')->references('id')->on('sales')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('sumber');
        });

        Schema::table('sewas', function (Blueprint $table) {
            $table->dropColumn('sumber');
        });

        Schema::table('kalibrasis', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->dropColumn(['sales_id', 'sumber']);
        });
    }
};
