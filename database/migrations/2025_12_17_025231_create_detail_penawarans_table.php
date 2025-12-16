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
        Schema::create('detail_penawarans', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('penawaran_id', 36)->nullable();
            $table->text('nama')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('harga')->nullable();
            $table->string('satuan', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penawarans');
    }
};
