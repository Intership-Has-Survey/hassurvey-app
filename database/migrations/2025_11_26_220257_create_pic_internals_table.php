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
        Schema::create('pic_internals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik')->nullable();
            $table->string('nama')->nullable();
            $table->string('email')->nullable();
            $table->string('nomor_wa')->nullable();
            $table->string('provinsi', 2)->nullable();
            $table->string('kota', 5)->nullable();
            $table->string('kecamatan', 8)->nullable();
            $table->string('desa', 13)->nullable();
            $table->text('detail_alamat')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pic_internals');
    }
};
