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
            $table->date('tgl_selesai');
            $table->string('provinsi', 2);
            $table->string('kota', 5);
            $table->string('kecamatan', 8);
            $table->string('desa', 13);
            $table->text('detail_alamat');
            $table->decimal('total_biaya', 15, 2)->nullable();
            $table->timestamps();

            $table->uuidMorphs('customer');
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
