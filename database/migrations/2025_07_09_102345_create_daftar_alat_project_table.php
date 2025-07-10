<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daftar_alat_project', function (Blueprint $table) {
            $table->string('status')->default('Terpakai');
            $table->timestamps();

            $table->primary(['daftar_alat_id', 'project_id']);

            $table->foreignUuid('daftar_alat_id')->constrained('daftar_alat')->onDelete('cascade');
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_alat_project');
    }
};
