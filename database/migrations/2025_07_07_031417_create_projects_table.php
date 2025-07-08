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
        Schema::create('projects', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->primary(); // UUID primary key
            $table->string('nama_project');
            $table->uuid('kategori_id');
            $table->string('sumber');
            $table->uuid('sales_id');
            $table->string('nama_klien');
            $table->string('jenis_penjualan');
            $table->string('level_company');
            $table->string('lokasi');
            $table->string('alamat');
            $table->string('status');
            $table->string('nilai_project');
            $table->string('tanggal_informasi_masuk');
            $table->string('nama_pic');
            $table->string('nomor_wa_pic');
            $table->string('status_pekerjaan_lapangan');
            $table->string('status_pembayaran');
            $table->timestamps();

            $table->foreign('kategori_id')->references('id')->on('kategoris')->onDelete('cascade');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
