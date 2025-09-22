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
        Schema::create('pemasukans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->decimal('jumlah', 15, 2);
            $table->string('deskripsi')->nullable();
            $table->timestamps();

            $table->foreignUuid('tabungan_id')
                ->constrained('tabungans')
                ->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukans');
    }
};
