ajuan<?php

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
                    $table->string('judul_pengajuan');
                    $table->text('deskripsi_pengajuan')->nullable();
                    $table->string('bank_id')->constrained('banks');
                    $table->string('bank_account_id')->constrained('bank_accounts');
                    $table->string('nilai');
                    $table->string('dalam_review')->nullable();
                    $table->string('ditolak')->nullable();
                    $table->string('disetujui')->nullable();
                    $table->string('alasan')->nullable();
                    $table->foreignUuid('user_id')->constrained('users');
                    $table->foreignUuid('level_id')->nullable();
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreignUuid('project_id')->nullable()->constrained('projects')->onDelete('cascade');
                    $table->foreignUuid('sewa_id')->nullable()->constrained('sewa')->onDelete('cascade');
                    $table->foreignUuid('penjualan_id')->nullable()->constrained('penjualans')->onDelete('cascade');
                    $table->foreignUuid('kalibrasi_id')->nullable()->constrained('kalibrasis')->onDelete('cascade');
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
