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
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            $table->uuidMorphs('pengajuanable');

            $table->unsignedInteger('dibayar')
                ->nullable()
                ->after('nilai');
            // hasilnya: pengajuanable_id, pengajuanable_type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_danas', function (Blueprint $table) {
            $table->dropColumn('dibayar');
            $table->dropMorphs('pengajuanable');
        });
    }
};
