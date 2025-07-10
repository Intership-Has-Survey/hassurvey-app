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
        Schema::create('daftar_alat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_alat');
            $table->string('jenis_alat');
            $table->string('merk');
            $table->string('kondisi')->default('Baik'); // default kondisi alat
            $table->string('status')->default('Tersedia'); // default status alat
            $table->text('keterangan')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_alat');
    }
};
