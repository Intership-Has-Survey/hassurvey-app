<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nama')->unique();
            $table->date('tanggal_penjualan');

            $table->foreignUuid('corporate_id')->nullable()->constrained('corporate');

            $table->foreignUuid('sales_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status_pembayaran')->nullable()->default('Belum Dibayar');

            $table->text('catatan')->nullable();
            $table->string('total_items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
