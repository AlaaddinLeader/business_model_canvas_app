<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->timestamp('generated_at')->useCurrent();
            $table->integer('version')->default(1);
            $table->timestamps();

            // Add unique constraint for project_id and version combination
            $table->unique(['project_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_models');
    }
};
