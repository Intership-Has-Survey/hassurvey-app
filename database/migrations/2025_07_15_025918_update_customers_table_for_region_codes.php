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
            if (!Schema::hasColumn('customers', 'province_code')) {
                $table->string('province_code', 2)->nullable()->after('telepon');
            }
            if (!Schema::hasColumn('customers', 'regency_code')) {
                $table->string('regency_code', 5)->nullable()->after('province_code');
            }
            if (!Schema::hasColumn('customers', 'district_code')) {
                $table->string('district_code', 8)->nullable()->after('regency_code');
            }
            if (!Schema::hasColumn('customers', 'village_code')) {
                $table->string('village_code', 13)->nullable()->after('district_code');
            }
            if (!Schema::hasColumn('customers', 'detail_alamat')) {
                $table->text('detail_alamat')->nullable()->after('village_code');
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
