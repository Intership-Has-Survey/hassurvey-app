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
            $table->decimal('harga_perhari', 15, 2)->nullable();
            $table->decimal('biaya_sewa_alat', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto_bukti')->nullable();
            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('daftar_alat_id')->constrained('daftar_alat')->onDelete('cascade');
            $table->foreignUuid('sewa_id')->constrained('sewa')->onDelete('cascade');
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
