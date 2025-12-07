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
        Schema::create('detail_invoices', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('invoice_id');
            $table->string('nama', 50);
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('diskon')->nulllable();
            $table->integer('pajak')->nulllable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_invoices');
    }
};
