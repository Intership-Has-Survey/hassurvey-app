<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Catatan: Ini adalah file migrasi BARU untuk membuat tabel 'customers'.
     * Anda harus membuat file ini terlebih dahulu.
     */
    public function up(): void
    {
        //TABEL PERORANGAN
        Schema::create('perorangan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->tel()->nullable();
            // $table->string('alamat');

            $table->string('provinsi', 2)->nullable();
            $table->string('kota', 5)->nullable();
            $table->string('kecamatan', 8)->nullable();
            $table->string('desa', 13)->nullable();
            $table->string('detail_alamat')->nullable();;

            $table->string('nik')->nullable();
            $table->text('foto_ktp')->nullable();
            $table->text('foto_kk')->nullable();
            $table->uuid('company_id')->nullable();

            $table->timestamps();
            $table->unique(['nik', 'company_id']);
            $table->unique(['email', 'company_id']);

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });

        //TABEL CORPORATE
        Schema::create('corporate', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('nib')->nullable();
            $table->string('level')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->tel()->nullable();

            $table->string('provinsi', 2)->nullable();
            $table->string('kota', 5)->nullable();
            $table->string('kecamatan', 8)->nullable();
            $table->string('desa', 13)->nullable();
            $table->string('detail_alamat')->nullable();
            $table->uuid('company_id')->nullable();

            $table->timestamps();
            $table->unique(['email', 'company_id']);
            $table->unique(['nib', 'company_id']);

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });

        //TABEL PIVOT PERORANGANCUSTOMER
        Schema::create('perorangan_corporate', function (Blueprint $table) {
            $table->primary(['perorangan_id', 'corporate_id']);
            $table->timestamps();

            $table->foreignUuid('perorangan_id')->constrained('perorangan');
            $table->foreignUuid('corporate_id')->constrained('corporate');
            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perorangan');
        Schema::dropIfExists('corporate');
        Schema::dropIfExists('perorangan_corporate');
    }
};
