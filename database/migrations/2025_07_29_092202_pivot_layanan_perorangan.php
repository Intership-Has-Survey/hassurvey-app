<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_perorangan', function (Blueprint $table) {
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->primary(['project_id', 'perorangan_id']);
            $table->string('peran');
            $table->timestamps();
        });

        Schema::create('sewa_perorangan', function (Blueprint $table) {
            $table->foreignUuid('sewa_id')->constrained('sewa')->cascadeOnDelete();
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->primary(['sewa_id', 'perorangan_id']);
            $table->string('peran');
            $table->timestamps();
        });

        Schema::create('kalibrasi_perorangan', function (Blueprint $table) {
            $table->foreignUuid('kalibrasi_id')->constrained('kalibrasis')->cascadeOnDelete();
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->primary(['kalibrasi_id', 'perorangan_id']);
            $table->string('peran');
            $table->timestamps();
        });

        Schema::create('penjualan_perorangan', function (Blueprint $table) {
            $table->foreignUuid('penjualan_id')->constrained('penjualans')->cascadeOnDelete();
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->primary(['penjualan_id', 'perorangan_id']);
            $table->string('peran');
            $table->timestamps();
        });

        Schema::create('alat_customers_perorangan', function (Blueprint $table) {
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->foreignUuid('alat_customers_id')->constrained('alat_customers')->cascadeOnDelete();
            $table->primary(['alat_customers_id', 'perorangan_id']);
            $table->string('peran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_perorangan');
        Schema::dropIfExists('sewa_perorangan');
        Schema::dropIfExists('kalibrasi_perorangan');
        Schema::dropIfExists('penjualan_perorangan');
        Schema::dropIfExists('alat_customers_perorangan');
    }
};
