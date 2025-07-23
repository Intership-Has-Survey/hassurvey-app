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
        Schema::table('riwayat_sewa', function (Blueprint $table) {
            // Drop foreign keys first by their generated names
            $table->dropForeign('riwayat_sewa_daftar_alat_id_foreign');
            $table->dropForeign('riwayat_sewa_sewa_id_foreign');

            // Drop the composite primary key
            $table->dropPrimary(['daftar_alat_id', 'sewa_id']);

            // Add the new UUID primary key
            $table->uuid('id')->primary()->first();

            // Re-add foreign key constraints
            $table->foreign('daftar_alat_id')->references('id')->on('daftar_alat')->onDelete('cascade');
            $table->foreign('sewa_id')->references('id')->on('sewa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_sewa', function (Blueprint $table) {
            // Drop foreign keys first by their generated names
            $table->dropForeign('riwayat_sewa_daftar_alat_id_foreign');
            $table->dropForeign('riwayat_sewa_sewa_id_foreign');

            // Drop the new primary key
            $table->dropColumn('id');

            // Re-add the composite primary key
            $table->primary(['daftar_alat_id', 'sewa_id']);

            // Re-add foreign key constraints
            $table->foreign('daftar_alat_id')->references('id')->on('daftar_alat')->onDelete('cascade');
            $table->foreign('sewa_id')->references('id')->on('sewa')->onDelete('cascade');
        });
    }
};
