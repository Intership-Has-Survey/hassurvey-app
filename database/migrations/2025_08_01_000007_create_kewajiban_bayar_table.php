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
        Schema::create('kewajiban_bayars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operasional_id');
            $table->unsignedBigInteger('bangunan_id')->nullable();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->string('bukti')->nullable();
            $table->timestamps();

            $table->foreign('operasional_id')->references('id')->on('operasionals')->onDelete('cascade');
            $table->foreign('bangunan_id')->references('id')->on('bangunans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kewajiban_bayars');
    }
};
