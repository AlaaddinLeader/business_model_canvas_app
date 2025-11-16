<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Customer Segment Types
        Schema::create('customer_segment_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Relationship Types
        Schema::create('relationship_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Channel Types
        Schema::create('channel_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Resource Types
        Schema::create('resource_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Revenue Stream Types
        Schema::create('revenue_stream_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Seed lookup data
        $this->seedLookupTables();
    }

    private function seedLookupTables(): void
    {
        // Customer Segment Types
        DB::table('customer_segment_types')->insert([
            ['code' => 'b2c', 'name' => 'Business to Consumer', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'b2b', 'name' => 'Business to Business', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'b2g', 'name' => 'Business to Government', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Relationship Types
        DB::table('relationship_types')->insert([
            ['code' => 'self_service', 'name' => 'Self Service', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'personal_assist', 'name' => 'Personal Assistance', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'automated', 'name' => 'Automated Service', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'co_creation', 'name' => 'Co-creation', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'community', 'name' => 'Community', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Channel Types
        DB::table('channel_types')->insert([
            ['code' => 'online_store', 'name' => 'Online Store', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'mobile_app', 'name' => 'Mobile App', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'physical_store', 'name' => 'Physical Store', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'agents', 'name' => 'Sales Agents', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'marketplace', 'name' => 'Marketplace', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Resource Types
        DB::table('resource_types')->insert([
            ['code' => 'human', 'name' => 'Human Resources', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'technical', 'name' => 'Technical Resources', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'equipment', 'name' => 'Equipment', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'intellectual', 'name' => 'Intellectual Property', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'financial', 'name' => 'Financial Resources', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Revenue Stream Types
        DB::table('revenue_stream_types')->insert([
            ['code' => 'sales', 'name' => 'Direct Sales', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'subscriptions', 'name' => 'Subscriptions', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ads', 'name' => 'Advertising', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'commissions', 'name' => 'Commissions', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'licensing', 'name' => 'Licensing', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'freemium', 'name' => 'Freemium', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_stream_types');
        Schema::dropIfExists('resource_types');
        Schema::dropIfExists('channel_types');
        Schema::dropIfExists('relationship_types');
        Schema::dropIfExists('customer_segment_types');
    }
};
