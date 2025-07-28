<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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

            $table->string('provinsi', 2)->nullable();
            $table->string('kota', 5)->nullable();
            $table->string('kecamatan', 8)->nullable();
            $table->string('desa', 13)->nullable();
            $table->string('detail_alamat');

            // keuangan & status
            $table->decimal('nilai_project', 15, 2)->default(0);
            $table->string('status');
            $table->string('status_pembayaran')->nullable()->default('Belum Dibayar');
            $table->string('status_pekerjaan')->nullable()->default('Belum Dikerjakan');
            $table->timestamps();

            // Relasi
            $table->foreignUuid('corporate_id')->nullable()->constrained('corporate');
            // $table->foreignUuid('perorangan_id')->constrained('perorangan');
            $table->foreignUuid('sewa_id')->nullable();
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
