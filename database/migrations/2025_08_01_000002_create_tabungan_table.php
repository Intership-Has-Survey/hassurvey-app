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
            $table->id();
            $table->string('nama');
            $table->decimal('nominal', 15, 2);
            $table->unsignedBigInteger('detailable_id');
            $table->string('detailable_type');
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('visi_mati_id')->constrained('visi_mati')->onDelete('cascade');
            $table->index(['detailable_id', 'detailable_type']);
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
