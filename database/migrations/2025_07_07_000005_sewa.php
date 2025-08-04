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
            $table->date('tgl_mulai');
            $table->date('tgl_selesai')->nullable();
            $table->string('rentang')->nullable();
            $table->string('provinsi', 2);
            $table->string('kota', 5);
            $table->string('kecamatan', 8);
            $table->string('desa', 13);
            $table->text('detail_alamat');

            $table->decimal('harga_perkiraan', 15, 2)->nullable();
            $table->decimal('harga_real', 15, 2)->nullable();
            $table->decimal('harga_fix', 15, 2)->nullable();

            $table->string('status')->default('Belum Selesai');
            $table->boolean('needs_replacement')->default(false);
            $table->boolean('is_locked')->default(false);

            $table->timestamps();

            $table->foreignUuid('corporate_id')->nullable()->constrained('corporate');
            // $table->foreignUuid('perorangan_id')->nullable()->constrained('perorangan');
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
