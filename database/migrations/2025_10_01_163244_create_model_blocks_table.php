<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('business_models')->onDelete('cascade');
            $table->string('block_name');
            $table->text('block_content')->nullable();
            $table->timestamps();

            // Add index for faster queries
            $table->index(['model_id', 'block_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_blocks');
    }
};
