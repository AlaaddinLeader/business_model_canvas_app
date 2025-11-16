<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_model_id')->constrained()->onDelete('cascade');
            $table->integer('version');
            $table->foreignId('channel_type_id')->constrained()->onDelete('cascade');
            $table->text('details')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_model_id', 'version']);
            $table->index('channel_type_id');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
