<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_perorangan', function (Blueprint $table) {
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->primary(['project_id', 'perorangan_id']);
            $table->timestamps();
        });

        Schema::create('sewa_perorangan', function (Blueprint $table) {
            $table->foreignUuid('sewa_id')->constrained('sewa')->cascadeOnDelete();
            $table->foreignUuid('perorangan_id')->constrained('perorangan')->cascadeOnDelete();
            $table->primary(['sewa_id', 'perorangan_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_perorangan');
    }
};
