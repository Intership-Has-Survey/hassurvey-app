<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daftar_alat_project', function (Blueprint $table) {
            $table->uuid('daftar_alat_id');
            $table->uuid('project_id');
            $table->unsignedBigInteger('user_id')->nullable(); // karena users.id = integer

            $table->string('status')->default('Terpakai'); // default saat alat dipakai project
            $table->timestamps();

            $table->primary(['daftar_alat_id', 'project_id']);

            $table->foreign('daftar_alat_id')->references('id')->on('daftar_alat')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_alat_project');
    }
};
