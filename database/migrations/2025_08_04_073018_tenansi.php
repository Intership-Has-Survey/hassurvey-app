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
        // Schema::table('kategoris', function (Blueprint $table) {
        //     $table->ForeignUuid('company_id');
        // });
        Schema::table('projects', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        // Schema::table('activity_log', function (Blueprint $table) {
        //     $table->ForeignUuid('company_id');
        // });
        Schema::table('transaksi_pembayarans', function (Blueprint $table) {
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
        Schema::table('personel', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('pemilik', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('alat_customers', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('corporate', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('perorangan', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('status_pembayarans', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('levels', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });
        Schema::table('riwayat_sewa', function (Blueprint $table) {
            $table->ForeignUuid('company_id');
        });


        $tableNames = config('permission.table_names', [
            'roles' => 'roles',
        ]);

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->uuid('company_id')->nullable()->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
