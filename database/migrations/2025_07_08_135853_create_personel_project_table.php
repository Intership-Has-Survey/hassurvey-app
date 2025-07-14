<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personel_project', function (Blueprint $table) {

            $table->primary(['project_id', 'personel_id']);

            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('personel_id')->constrained('personel')->cascadeOnDelete();

            $table->string('peran');
            $table->timestamps();
            $table->foreignUuid('user_id')->constrained('users');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personel_project');
    }
};
