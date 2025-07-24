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
        Schema::create('jenis_alat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });

        Schema::create('merk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();

            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->softDeletes();

        });

        Schema::create('daftar_alat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_seri')->unique();

            $table->foreignUuid('jenis_alat_id')->constrained('jenis_alat');
            $table->foreignUuid('merk_id')->constrained('merk');

            // Mengubah kolom 'kondisi' menjadi boolean
            // true: Baik, false: Bermasalah
            $table->boolean('kondisi')->default(true);

            // Mengubah kolom 'status' menjadi boolean
            // true: Tersedia, false: Tidak Tersedia
            $table->boolean('status')->default(true);

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('pemilik_id')->constrained('pemilik');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_alat');
        Schema::dropIfExists('jenis_alat');
        Schema::dropIfExists('merk');
    }
};
