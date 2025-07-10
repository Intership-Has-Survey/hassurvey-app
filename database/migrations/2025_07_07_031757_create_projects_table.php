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
            $table->uuid('id')->primary();
            // info utama
            $table->string('nama_project');
            $table->foreignUuid('kategori_id')->constrained('kategoris');
            $table->uuid('sales_id')->constrained('sales');
            $table->date('tanggal_informasi_masuk');
            $table->string('sumber');
            // customer 
            $table->uuid('customer_id')->constrained('customers');
            $table->string('jenis_penjualan');
            $table->string('nama_institusi')->nullable();
            $table->string('level_company')->nullable();
            $table->string('lokasi');
            $table->string('alamat');
            // keuangan & status
            $table->decimal('nilai_project', 15, 2)->default(0);
            $table->string('status');
            $table->string('status_pembayaran')->nullable()->default('Belum Dibayar');
            $table->string('status_pekerjaan_lapangan')->nullable()->default('Belum Dikerjakan');
            $table->timestamps();

            // Relasi
            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
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
