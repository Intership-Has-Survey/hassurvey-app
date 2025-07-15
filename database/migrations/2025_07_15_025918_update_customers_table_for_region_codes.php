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
        Schema::table('customers', function (Blueprint $table) {
            // Hapus kolom alamat yang lama jika masih ada
            if (Schema::hasColumn('customers', 'alamat')) {
                $table->dropColumn('alamat');
            }

            // Tambahkan kolom baru, periksa dulu jika sudah ada
            if (!Schema::hasColumn('customers', 'provinsi')) {
                $table->string('provinsi', 2)->nullable()->after('telepon');
            }
            if (!Schema::hasColumn('customers', 'kota')) {
                $table->string('kota', 5)->nullable()->after('provinsi');
            }
            if (!Schema::hasColumn('customers', 'kecamatan')) {
                $table->string('kecamatan', 8)->nullable()->after('kota');
            }
            if (!Schema::hasColumn('customers', 'desa')) {
                $table->string('desa', 13)->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('customers', 'detail_alamat')) {
                $table->text('detail_alamat')->nullable()->after('desa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'alamat')) {
                $table->text('alamat');
            }

            $columnsToDrop = ['provinsi', 'kota', 'kecamatan', 'desa', 'detail_alamat'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
