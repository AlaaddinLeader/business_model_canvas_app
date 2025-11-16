<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_model_id')->constrained()->onDelete('cascade');
            $table->integer('version');
            $table->string('expense_type', 100)->comment('e.g., Raw Materials, Labor, Rent');
            $table->text('description')->nullable();
            $table->decimal('unit_cost', 15, 2)->default(0)->comment('Cost per unit');
            $table->decimal('quantity', 10, 2)->default(1)->comment('Number of units');
            $table->decimal('total', 15, 2)->storedAs('unit_cost * quantity')->comment('Calculated total');
            $table->string('currency_code', 3)->default('USD');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_model_id', 'version', 'display_order']);
            $table->index(['business_model_id', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
