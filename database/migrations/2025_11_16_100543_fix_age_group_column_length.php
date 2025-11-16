<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix key_resources table
        Schema::table('key_resources', function (Blueprint $table) {
            $table->unsignedBigInteger('resource_type_id')->nullable()->change();
        });

        // Fix channels table
        Schema::table('channels', function (Blueprint $table) {
            $table->unsignedBigInteger('channel_type_id')->nullable()->change();
        });

        // Fix customer_relationships table
        Schema::table('customer_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('relationship_type_id')->nullable()->change();
        });

        // Fix customer_segments table
        Schema::table('customer_segments', function (Blueprint $table) {
            $table->unsignedBigInteger('segment_type_id')->nullable()->change();
        });

        // Fix revenue_streams table
        Schema::table('revenue_streams', function (Blueprint $table) {
            $table->unsignedBigInteger('stream_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('key_resources', function (Blueprint $table) {
            $table->unsignedBigInteger('resource_type_id')->nullable(false)->change();
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->unsignedBigInteger('channel_type_id')->nullable(false)->change();
        });

        Schema::table('customer_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('relationship_type_id')->nullable(false)->change();
        });

        Schema::table('customer_segments', function (Blueprint $table) {
            $table->unsignedBigInteger('segment_type_id')->nullable(false)->change();
        });

        Schema::table('revenue_streams', function (Blueprint $table) {
            $table->unsignedBigInteger('stream_type_id')->nullable(false)->change();
        });
    }
};
