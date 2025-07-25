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
        Schema::create('riwayat_sewa', function (Blueprint $table) {
            $table->primary(['daftar_alat_id', 'sewa_id']);
            $table->dateTime('tgl_keluar')->nullable();
            $table->dateTime('tgl_masuk')->nullable();
            $table->string('kondisi_kembali')->default('Baik');

            $table->decimal('harga_perhari', 15, 2)->nullable();
            $table->decimal('biaya_perkiraan_alat', 15, 2)->nullable();
            $table->decimal('biaya_sewa_alat', 15, 2)->nullable();
            $table->decimal('pendapataninv', 15, 2)->nullable();
            $table->decimal('pendapatanhas', 15, 2)->nullable();

            $table->text('keterangan')->nullable();
            
            $table->string('foto_bukti')->nullable();
            $table->string('recordId')->nullable(); // Tambahkan kolom recordId untuk menyimpan ID record terkait
            $table->boolean(('needs_replacement'))->default(false); // Tambahkan kolom untuk kebutuhan penggantian alat
            $table->integer('diskon_hari')->nullable(); // Tambahkan kolom untuk
            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('daftar_alat_id')->constrained('daftar_alat')->onDelete('cascade');
            $table->foreignUuid('sewa_id')->constrained('sewa')->onDelete('cascade');
            $table->foreignUuid('project_id')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_sewa');
    }
};
