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
        Schema::create('operasional', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->longText('deskripsi');

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tabungan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->longText('deskripsi');

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('visi_mati', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->longText('deskripsi');

            
            $table->foreignUuid('operasional_id')->constrained('operasional');
            $table->foreignUuid('tabungan_id')->constrained('tabungan');
            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visi_mati');
        Schema::dropIfExists('operasional');
        Schema::dropIfExists('tabungan');
    }
};
