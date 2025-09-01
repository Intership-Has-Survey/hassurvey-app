<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nama_penjualan');
            $table->date('tanggal_penjualan');

            $table->foreignUuid('corporate_id')->nullable()->constrained('corporate');

            $table->foreignUuid('sales_id')->nullable()->constrained('sales');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status_pembayaran')->nullable()->default('Belum Dibayar');

            $table->text('catatan')->nullable();
            $table->integer('total_items')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
