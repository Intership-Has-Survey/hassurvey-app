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
        Schema::create('pengajuan_danas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignUuid('sewa_id')->nullable()->constrained('sewa')->onDelete('cascade');
            $table->string('judul_pengajuan');
            $table->text('deskripsi_pengajuan')->nullable();
            $table->string('bank_id')->constrained('banks');
            $table->string('bank_account_id')->constrained('bank_accounts');
            $table->decimal('nilai', 15, 2);
            // $table->string('nomor_rekening')->nullable();
            // $table->string('nama_pemilik_rekening')->nullable();
            $table->string('dalam_review');
            $table->string('ditolak');
            $table->string('disetujui');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('level_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_danas');
    }
};
