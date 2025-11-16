<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_model_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('change_type', ['created', 'updated', 'deleted'])->default('updated');
            $table->string('table_name', 100)->comment('Which table was changed');
            $table->unsignedBigInteger('record_id')->nullable()->comment('ID of changed record');
            $table->string('field_name', 100)->nullable()->comment('Which field was changed');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('changed_at')->useCurrent();

            // Performance indexes
            $table->index(['business_model_id', 'changed_at']);
            $table->index(['table_name', 'record_id']);
            $table->index(['user_id', 'changed_at']);
            $table->index('change_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_changes');
    }
};
