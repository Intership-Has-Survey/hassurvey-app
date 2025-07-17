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
        /*Customer dipanggil menggunakan Morph, Customer harus dibuat dari jasa lain
        tidak bisa dibuat sendiri. Maka dari itu, Customer yang terbagi menjadi 2 jenis
        yaitu corporate dan perorangan digabung di satu migrasi. Kondisi PIC Perusahaan
         yang sama dengan Customer maka mengakibatkan relasi many to many antara perusahaan
         dan customer, pivot antar keduanya juga dibaut di migrasi ini sehingga total ada
         3 Migrasi*/

        //TABEL PERORANGAN
        Schema::create('perorangan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('gender');
            $table->string('email')->unique();
            $table->string('telepon')->tel();
            // $table->string('alamat');

            $table->string('provinsi', 2)->nullable();
            $table->string('kota', 5)->nullable();
            $table->string('kecamatan', 8)->nullable();
            $table->string('desa', 13)->nullable();
            $table->string('detail_alamat');

            $table->string('nik')->nullable()->unique();
            $table->text('foto_ktp')->nullable();
            $table->text('foto_kk')->nullable();

            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });

        //TABEL CORPORATE
        Schema::create('corporate', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('nib')->unique()->nullable();
            $table->string('level');
            $table->string('email')->unique();
            $table->string('telepon')->tel();
            // $table->string('alamat');

            $table->string('provinsi', 2);
            $table->string('kota', 5);
            $table->string('kecamatan', 8);
            $table->string('desa', 13);
            $table->string('detail_alamat');

            $table->timestamps();

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
