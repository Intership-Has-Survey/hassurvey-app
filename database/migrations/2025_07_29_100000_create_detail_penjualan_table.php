<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->uuid('id');

            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal_item', 15, 2);

            $table->timestamps();

            $table->foreignUuid('penjualan_id')->references('id')->on('penjualans')->onDelete('cascade');
            $table->foreignUuid('daftar_alat_id')->references('id')->on('daftar_alat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
