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
        Schema::create('transaksi_pembayarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payable_id');
            $table->string('payable_type')->nullable();
            $table->foreignUuid('user_id')->constrained('users');
            $table->decimal('nilai', 15, 2);
            $table->date('tanggal_transaksi');
            $table->string('metode_pembayaran');
            $table->string('bukti_pembayaran_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pembayarans');
    }
};
