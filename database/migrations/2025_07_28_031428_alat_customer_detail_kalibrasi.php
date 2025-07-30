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
        //

        Schema::create('alat_customer_detail_kalibrasi', function (Blueprint $table) {
            $table->Uuid('id');
            $table->foreignUuid('detail_kalibrasi_id');
            $table->foreignUuid('alat_customer_id');
            $table->date('tgl_masuk');
            $table->date('tgl_stiker_kalibrasi');
            $table->date('tgl_keluar');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
