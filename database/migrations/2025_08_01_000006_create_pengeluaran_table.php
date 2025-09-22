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
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tabungan_id')->nullable();
            $table->foreign('tabungan_id')->references('id')->on('tabungans')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('jumlah', 15, 2);
            $table->text('deskripsi')->nullable();
            $table->uuidMorphs('pengeluaranable'); // Adds pengeluaranable_id and pengeluaranable_type
            $table->timestamps();

            $table->foreignUuid('visi_mati_id')->constrained('visi_mati')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
