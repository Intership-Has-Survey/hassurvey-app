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
        Schema::create('status_pekerjaans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->enum('pekerjaan', ['Pekerjaan Lapangan', 'Input Data', 'Laporan']);
            $table->enum('status', ['Belum Selesai', 'Tidak Selesai', 'Selesai', 'Tidak Perlu']);
            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pekerjaans');
        Schema::dropIfExists('peralatan_kerjas');
        Schema::dropIfExists('personels');
        Schema::dropIfExists('projects');
    }
};
