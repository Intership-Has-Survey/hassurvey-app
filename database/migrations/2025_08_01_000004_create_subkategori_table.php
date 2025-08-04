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
        Schema::create('subcategorizables', function (Blueprint $table) {
            $table->Uuid('visi_mati_id');
            $table->Uuid('subcategorizable_id');
            $table->string('subcategorizable_type');
            $table->primary(['visi_mati_id', 'subcategorizable_id', 'subcategorizable_type'], 'subcategorizables_primary');

            $table->foreign('visi_mati_id')->references('id')->on('visi_mati')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategorizables');
    }
};
