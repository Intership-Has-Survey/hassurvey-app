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
        Schema::create('penawarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_penawaran')->nullable();
            $table->string('customer_id', 50)->nullable();
            $table->string('customer_type', 50)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('status', 25)->nullable();
            $table->string('company_id', 36)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawarans');
    }
};
