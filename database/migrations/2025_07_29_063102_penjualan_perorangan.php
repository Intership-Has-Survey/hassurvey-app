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
        Schema::create('penjualan_perorangan', function (Blueprint $table) {
            $table->foreignUuid('penjualan_id')->constrained('penjualans')->cascadeOnDelete();
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->primary(['penjualan_id', 'perorangan_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_perorangan');
    }
};
