<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_model_id')->constrained()->onDelete('cascade');
            $table->integer('version');
            $table->foreignId('stream_type_id')->constrained('revenue_stream_types')->onDelete('cascade');
            $table->text('details')->nullable();
            $table->decimal('projected_amount', 15, 2)->nullable()->comment('Projected revenue amount');
            $table->string('currency_code', 3)->default('USD');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_model_id', 'version']);
            $table->index('stream_type_id');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_streams');
    }
};
