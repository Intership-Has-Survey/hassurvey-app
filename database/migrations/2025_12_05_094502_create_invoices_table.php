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


        // Schema::create('invoices', function (Blueprint $table) {
        //     $table->uuid('id')->primary();

        //     $table->string('kode_invoice')->nullable();

        //     // Morph Customer
        //     $table->string('customer_id', 50)->nullable();
        //     $table->string('customer_type', 50)->nullable();

        //     $table->string('telepon', 15)->nullable();
        //     $table->string('email', 50)->nullable();

        //     $table->date('tanggal_mulai')->nullable();
        //     $table->date('tanggal_selesai')->nullable();

        //     $table->string('status', 15)->nullable();
        //     $table->string('jenis', 20)->nullable();

        //     $table->char('company_id', 36)->nullable();

        //     $table->integer('ppn')->nullable();
        //     $table->integer('jumlah_pembayaran')->nullable();

        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
