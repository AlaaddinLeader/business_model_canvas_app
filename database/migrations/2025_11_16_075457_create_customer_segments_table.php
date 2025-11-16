<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_model_id')->constrained()->onDelete('cascade');
            $table->integer('version');
            $table->foreignId('segment_type_id')->nullable()->constrained('customer_segment_types')->onDelete('set null');
            $table->enum('age_group', ['children', 'youth', 'adults', 'seniors', 'all_ages'])->nullable();
            $table->enum('region', ['local', 'national', 'international'])->default('local');
            $table->text('problems')->nullable()->comment('Customer problems/pain points');
            $table->text('needs')->nullable()->comment('Customer needs/jobs to be done');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_model_id', 'version']);
            $table->index('segment_type_id');
            $table->index('region');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_segments');
    }
};
