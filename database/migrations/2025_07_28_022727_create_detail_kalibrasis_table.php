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
        Schema::create('detail_kalibrasis', function (Blueprint $table) {
            $table->Uuid('id');
            $table->foreignUuid('kalibrasi_id');
            $table->foreignUuid('alat_customer_id');
            $table->date('tgl_masuk');
            $table->date('tgl_stiker_kalibrasi')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kalibrasis');
    }
};
