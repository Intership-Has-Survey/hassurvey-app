<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('penjualan_id')->references('id')->on('penjualans')->onDelete('cascade');
            $table->foreignUuid('jenis_alat_id')->references('id')->on('jenis_alat');
            $table->foreignUuid('daftar_alat_id')->references('id')->on('daftar_alat');
            $table->foreignUuid('merk_id')->references('id')->on('merk');

            $table->string('harga');
            $table->string('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
