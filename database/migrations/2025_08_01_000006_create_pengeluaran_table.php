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
            $table->foreignUuid('tabungan_id')->constrained('tabungans')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('jumlah', 15, 2);
            $table->text('deskripsi')->nullable();
            $table->uuidMorphs('pengeluaranable'); // Adds pengeluaranable_id and pengeluaranable_type
            $table->timestamps();
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
