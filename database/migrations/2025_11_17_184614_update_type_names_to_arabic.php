<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحديث أسماء العلاقات مع العملاء
        $relationshipTranslations = [
            'self_service' => 'خدمة ذاتية',
            'personal_assist' => 'مساعدة شخصية',
            'automated' => 'خدمة آلية',
            'co_creation' => 'شراكة مع العميل',
        ];

        foreach ($relationshipTranslations as $code => $name) {
            DB::table('relationship_types')
                ->where('code', $code)
                ->update(['name' => $name]);
        }

        // تحديث أسماء القنوات
        $channelTranslations = [
            'online_store' => 'متجر إلكتروني',
            'mobile_app' => 'تطبيق جوال',
            'physical_store' => 'متجر فعلي',
            'agents' => 'مندوبين أو وكلاء',
        ];

        foreach ($channelTranslations as $code => $name) {
            DB::table('channel_types')
                ->where('code', $code)
                ->update(['name' => $name]);
        }

        // تحديث أسماء الموارد
        $resourceTranslations = [
            'human' => 'موارد بشرية',
            'technical' => 'موارد تقنية',
            'equipment' => 'معدات',
            'other' => 'موارد أخرى',
        ];

        foreach ($resourceTranslations as $code => $name) {
            DB::table('resource_types')
                ->where('code', $code)
                ->update(['name' => $name]);
        }

        // تحديث أسماء مصادر الدخل
        $incomeTranslations = [
            'sales' => 'مبيعات مباشرة',
            'subscriptions' => 'اشتراكات',
            'ads' => 'إعلانات',
            'commissions' => 'عمولات',
        ];

        foreach ($incomeTranslations as $code => $name) {
            DB::table('revenue_stream_types')
                ->where('code', $code)
                ->update(['name' => $name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // يمكن عكس التغييرات إذا لزم الأمر
        // لكن عادة لا نحتاج لذلك في هذه الحالة
    }
};
