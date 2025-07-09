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

            // Informasi Dasar Proyek
            $table->string('nama_project');
            $table->string('sumber'); // Contoh: Online, Offline
            $table->string('lokasi'); // Lokasi spesifik proyek
            $table->date('tanggal_informasi_masuk');

            // Relasi (Foreign Keys)
            $table->foreignUuid('kategori_id')->constrained('kategoris');
            $table->foreignUuid('sales_id')->constrained('sales');
            $table->foreignUuid('customer_id')->constrained('customers');
            $table->foreignId('user_id')->constrained('users'); // Relasi ke pembuat/pengelola

            // Status & Keuangan
            $table->string('status'); // Status Prospek: Prospect, Follow up, Closing
            $table->decimal('nilai_project', 15, 2)->default(0); // Menggunakan decimal untuk uang

            // Kolom Status Otomatis (diisi oleh sistem/observer)
            $table->string('status_pekerjaan_lapangan')->nullable()->default('Belum Dikerjakan');
            $table->string('status_pembayaran')->nullable()->default('Belum Dibayar');

            $table->timestamps();
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
