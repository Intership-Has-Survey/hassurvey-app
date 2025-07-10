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
        Schema::create('daftar_alat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_alat');
            $table->string('jenis_alat');
            $table->string('merk');
            $table->string('kondisi');
            $table->string('status');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_alat');
    }
};
