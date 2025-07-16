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
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('telepon');
            $table->string('provinsi', 2);
            $table->string('kota', 5);
            $table->string('kecamatan', 8);
            $table->string('desa', 13);
            $table->text('detail_alamat');
            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
