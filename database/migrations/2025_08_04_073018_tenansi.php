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
        Schema::table('kategoris', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('sewa', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('kalibrasis', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('penjualans', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('daftar_alat', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });

        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
