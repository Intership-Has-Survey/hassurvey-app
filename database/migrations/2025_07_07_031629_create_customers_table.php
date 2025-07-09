<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Catatan: Ini adalah file migrasi BARU untuk membuat tabel 'customers'.
     * Anda harus membuat file ini terlebih dahulu.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_pic');
            $table->string('tipe_customer');
            $table->string('nama_institusi')->nullable();
            $table->string('email')->unique();
            $table->string('telepon');
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
