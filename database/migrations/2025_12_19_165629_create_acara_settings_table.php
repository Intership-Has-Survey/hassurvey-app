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
        Schema::create('acara_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_perusahaan')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->text('header')->nullable();
            $table->text('footer')->nullable();
            $table->string('company_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acara_settings');
    }
};
