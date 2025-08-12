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
        Schema::create('pembayaran_personels', function (Blueprint $table) {
            $table->Uuid('id');
            $table->foreignUuid('personel_project_id')->nullable();
            $table->foreignUuid('project_id')->nullable();
            $table->foreignUuid('personel_id')->nullable();
            $table->foreignUuid('payable_id')->nullable();
            $table->string('payable_type')->nullable();
            $table->string('keterangan')->nullable();
            $table->date('tanggal_transaksi');
            $table->string('metode_pembayaran');
            $table->integer('nilai');
            $table->string('bukti_pembayaran_path')->nullable();
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_personels');
    }
};
