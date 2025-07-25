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
        Schema::create('status_pembayarans', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('nama_pembayaran');
            $table->foreignUuid('payable_id');
            $table->string('payable_type')->nullable();
            $table->string('jenis_pembayaran');
            $table->decimal('nilai', 15, 2)->default(0);


            $table->foreignUuid('user_id')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pembayarans');
    }
};
