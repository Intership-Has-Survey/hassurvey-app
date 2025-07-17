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
            // keuangan & status
            $table->decimal('nilai_project', 15, 2)->default(0);
            $table->string('status');
            $table->string('status_pembayaran')->nullable()->default('Belum Dibayar');
            $table->string('status_pekerjaan')->nullable()->default('Belum Selesai');
            $table->timestamps();

            // Alamat lokasi project
            $table->string('provinsi', 2);
            $table->string('kota', 5);
            $table->string('kecamatan', 8);
            $table->string('desa', 13);
            $table->text('detail_alamat');

            // Relasi
            $table->uuidMorphs('customer');
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
