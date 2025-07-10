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
            // Primary Key
            $table->uuid('id')->primary();
            $table->string('nama_project');
            $table->foreignUuid('kategori_id')->constrained('kategoris');
            $table->string('sumber'); // Contoh: Online, Offline
            $table->uuid('sales_id')->constrained('sales');
            $table->uuid('customer_id')->constrained('customers');
            $table->string('jenis_penjualan');
            $table->string('lokasi'); // Lokasi spesifik proyek
            $table->string('alamat');
            $table->string('status'); // Status Prospek: Prospect, Follow up, Closing
            $table->decimal('nilai_project', 15, 2)->default(0); // Menggunakan decimal untuk uang
            $table->date('tanggal_informasi_masuk');
            $table->string('status_pekerjaan_lapangan')->nullable()->default('Belum Dikerjakan');
            $table->string('status_pembayaran')->nullable()->default('Belum Dibayar');
            $table->timestamps();
            // Informasi Dasar Proyek

            // Relasi (Foreign Keys)
            $table->unsignedBigInteger('user_id')->constrained('users'); // Relasi ke pembuat/pengelola
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Status & Keuangan

            // Kolom Status Otomatis (diisi oleh sistem/observer)

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
