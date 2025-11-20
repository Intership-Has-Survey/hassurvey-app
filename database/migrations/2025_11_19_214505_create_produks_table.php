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
        Schema::create('produks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_seri');
            $table->uuid('company_id')->nullable();
            $table->integer('status')->default('1');
            $table->string('keterangan')->nullable();
            $table->foreignUuid('jenis_alat_id')->constrained('jenis_alat');
            $table->foreignUuid('merk_id')->constrained('merk');
            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
