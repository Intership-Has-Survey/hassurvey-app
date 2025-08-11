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
        Schema::create('pemilik', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('gender');
            $table->string('NIK');
            $table->string('email');
            $table->string('telepon');

            $table->unique(['nik', 'company_id']);
            $table->unique(['email', 'company_id']);

            $table->string('provinsi', 2)->nullable();
            $table->string('kota', 5)->nullable();
            $table->string('kecamatan', 8)->nullable();
            $table->string('desa', 13)->nullable();
            $table->string('detail_alamat')->nullable();

            $table->integer('persen_bagihasil')->default(20);
            $table->decimal('total_pendapatanktr')->default(0);
            $table->decimal('total_pendapataninv')->default(0);
            $table->decimal('total_pendapatanhas')->default(0);
            $table->decimal('total_tagihan')->default(0);

            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilik');
    }
};
