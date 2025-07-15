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
        Schema::create('sewa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('judul');
            $table->string('jenis');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('lokasi');
            $table->text('alamat');
            $table->decimal('total_biaya', 15, 2)->nullable();
            $table->timestamps();

            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sewa');
    }
};
