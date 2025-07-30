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
        Schema::create('kalibrasis', function (Blueprint $table) {
            $table->Uuid('id')->primary();
            $table->string('nama');
            $table->foreignUuid('corporate_id')->nullable()->constrained('corporate');
            // $table->foreignUuid('customer_id');
            $table->decimal('harga', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasis');
    }
};
