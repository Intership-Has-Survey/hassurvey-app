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
        Schema::create('personel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('nik');
            $table->string('nomor_wa');
            $table->string('tipe_personel');
            $table->string('jabatan');
            $table->string('keterangan')->nullable();
            $table->string('provinsi', 2)->nullable();
            $table->string('kota', 5)->nullable();
            $table->string('kecamatan', 8)->nullable();
            $table->string('desa', 13)->nullable();
            $table->text('detail_alamat')->nullable();
            $table->timestamps();

            $table->unique(['nik', 'company_id']);

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personel');
    }
};
