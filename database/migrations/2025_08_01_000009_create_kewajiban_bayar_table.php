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
        Schema::create('kewajiban_bayars', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('operasional_id')->nullable()->constrained('operasionals')->onDelete('cascade');
            $table->foreignUuid('penerima_operasional_id')->nullable()->constrained('penerima_operasionals')->onDelete('set null');
            
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->string('bukti')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kewajiban_bayars');
    }
};
