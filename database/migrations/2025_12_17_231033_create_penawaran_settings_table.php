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
        Schema::create('penawaran_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_perusahaan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('catatan')->nullable();
            $table->text('penutup')->nullable();
            $table->string('signature_name')->nullable();
            $table->string('nama')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('company_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawaran_settings');
    }
};
