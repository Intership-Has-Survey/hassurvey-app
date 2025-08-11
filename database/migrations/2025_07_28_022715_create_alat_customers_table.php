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
        Schema::create('alat_customers', function (Blueprint $table) {
            $table->Uuid('id')->primary();
            $table->foreignUuid('jenis_alat_id');
            $table->foreignUuid('merk_id');
            $table->string('nomor_seri');
            $table->unique(['nomor_seri', 'company_id']);
            $table->boolean('kondisi')->default(true);
            $table->text('keterangan')->nullable();
            // $table->foreignUuid('customer_id');
            $table->foreignUuid('corporate_id')->nullable()->constrained('corporate');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat_customers');
    }
};
