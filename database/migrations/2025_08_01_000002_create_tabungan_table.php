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
        Schema::create('tabungans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->decimal('target_nominal', 15, 2);
            $table->enum('target_tipe', ['orang', 'bangunan']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('visi_mati_id')->constrained('visi_mati')->onDelete('cascade');
            $table->foreignUuid('company_id')->constrained('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungans');
    }
};
