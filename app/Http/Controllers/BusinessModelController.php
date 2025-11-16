<?php

namespace App\Http\Controllers;

use App\Models\BusinessModel;
use App\Models\Project;
use App\Models\ValueProposition;
use App\Models\CustomerSegment;
use App\Models\CustomerSegmentType;
use App\Models\CustomerRelationship;
use App\Models\RelationshipType;
use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\KeyActivity;
use App\Models\KeyResource;
use App\Models\ResourceType;
use App\Models\KeyPartnership;
use App\Models\RevenueStream;
use App\Models\RevenueStreamType;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BusinessModelController extends Controller
{
    /**
     * عرض صفحة إدخال البيانات
     */
    public function index()
    {
        return view('pages.data-input');
    }

    /**
     * معالجة البيانات وإنشاء نموذج الأعمال
     */
    public function generate(Request $request)
    {
        $user = Auth::user();
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'value_proposition' => 'required|string',
            'age_group' => 'nullable|string',
            'region' => 'required|string',
            'target_notes' => 'nullable|string',
            'relationship' => 'required|string',
            'relationship_other' => 'nullable|string',
            'channels' => 'required|array|min:1',
            'channel_other' => 'nullable|string',
            'key_activities' => 'required|string',
            'resources' => 'required|array|min:1',
            'human_details' => 'nullable|string',
            'technical_details' => 'nullable|string',
            'equipment_details' => 'nullable|string',
            'resource_other_details' => 'nullable|string',
            'key_partners' => 'nullable|string',
            'income' => 'required|array|min:1',
            'income_other' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // 1. إنشاء مشروع جديد لكل نموذج
            $project = Project::create([
                'user_id' => $user->id,
                'name' => 'مشروع ' . $user->name . ' - ' . now()->format('Y-m-d H:i'),
                'description' => 'مشروع تم إنشاؤه تلقائياً'
            ]);

            // 2. إنشاء نموذج الأعمال
            $businessModel = BusinessModel::create([
                'project_id' => $project->id,
                'version' => 1,
                'currency_code' => $validated['currency'],
                'is_active' => true,
            ]);

            // 3. القيمة المضافة (Value Proposition)
            ValueProposition::create([
                'business_model_id' => $businessModel->id,
                'version' => 1,
                'content' => $validated['value_proposition'],
            ]);

            // 4. الشريحة المستهدفة (Customer Segment)
            CustomerSegment::create([
                'business_model_id' => $businessModel->id,
                'version' => 1,
                'segment_type_id' => null,
                'age_group' => $validated['age_group'],
                'region' => $validated['region'],
                'notes' => $validated['target_notes'],
            ]);

            // 5. العلاقات مع العملاء (Customer Relationship)
            $relationshipTypeId = null;
            if ($validated['relationship'] !== 'other') {
                $relationshipType = RelationshipType::firstOrCreate(
                    ['code' => $validated['relationship']],
                    ['name' => $validated['relationship']]
                );
                $relationshipTypeId = $relationshipType->id;
            }

            CustomerRelationship::create([
                'business_model_id' => $businessModel->id,
                'version' => 1,
                'relationship_type_id' => $relationshipTypeId,
                'details' => $validated['relationship'] === 'other'
                    ? $validated['relationship_other']
                    : null,
            ]);

            // 6. القنوات (Channels)
            foreach ($validated['channels'] as $channel) {
                if ($channel === 'other') {
                    Channel::create([
                        'business_model_id' => $businessModel->id,
                        'version' => 1,
                        'channel_type_id' => null,
                        'details' => $validated['channel_other'],
                    ]);
                } else {
                    $channelType = ChannelType::firstOrCreate(
                        ['code' => $channel],
                        ['name' => $channel]
                    );

                    Channel::create([
                        'business_model_id' => $businessModel->id,
                        'version' => 1,
                        'channel_type_id' => $channelType->id,
                        'details' => null,
                    ]);
                }
            }

            // 7. الأنشطة الرئيسية (Key Activities)
            KeyActivity::create([
                'business_model_id' => $businessModel->id,
                'version' => 1,
                'description' => $validated['key_activities'],
            ]);

            // 8. الموارد الرئيسية (Key Resources)
            $resourceMapping = [
                'human' => 'human_details',
                'technical' => 'technical_details',
                'equipment' => 'equipment_details',
                'other' => 'resource_other_details',
            ];

            foreach ($validated['resources'] as $resource) {
                $detailsField = $resourceMapping[$resource] ?? null;
                $details = $detailsField && isset($validated[$detailsField])
                    ? $validated[$detailsField]
                    : null;

                // تخطي إذا لم يكن هناك تفاصيل
                if (empty($details)) {
                    continue;
                }

                if ($resource !== 'other') {
                    $resourceType = ResourceType::firstOrCreate(
                        ['code' => $resource],
                        ['name' => $resource]
                    );

                    KeyResource::create([
                        'business_model_id' => $businessModel->id,
                        'version' => 1,
                        'resource_type_id' => $resourceType->id,
                        'details' => $details,
                    ]);
                } else {
                    // للموارد "أخرى"، نحتاج إلى إنشاء نوع أو استخدام null إذا كان مسموحاً
                    $resourceType = ResourceType::firstOrCreate(
                        ['code' => 'other'],
                        ['name' => 'أخرى']
                    );

                    KeyResource::create([
                        'business_model_id' => $businessModel->id,
                        'version' => 1,
                        'resource_type_id' => $resourceType->id,
                        'details' => $details,
                    ]);
                }
            }

            // 9. الشراكات الرئيسية (Key Partnerships)
            if (!empty($validated['key_partners'])) {
                KeyPartnership::create([
                    'business_model_id' => $businessModel->id,
                    'version' => 1,
                    'description' => $validated['key_partners'],
                ]);
            }

            // 10. مصادر الدخل (Revenue Streams)
            foreach ($validated['income'] as $income) {
                if ($income === 'other') {
                    RevenueStream::create([
                        'business_model_id' => $businessModel->id,
                        'version' => 1,
                        'stream_type_id' => null,
                        'details' => $validated['income_other'],
                        'currency_code' => $validated['currency'],
                    ]);
                } else {
                    $streamType = RevenueStreamType::firstOrCreate(
                        ['code' => $income],
                        ['name' => $income]
                    );

                    RevenueStream::create([
                        'business_model_id' => $businessModel->id,
                        'version' => 1,
                        'stream_type_id' => $streamType->id,
                        'currency_code' => $validated['currency'],
                    ]);
                }
            }

            // 11. هيكل التكلفة (Expenses)
            $displayOrder = 1;
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^expense_type_(\d+)$/', $key, $matches)) {
                    $rowNum = $matches[1];
                    $expenseType = $request->input("expense_type_{$rowNum}");
                    $description = $request->input("expense_desc_{$rowNum}");
                    $unitCost = $request->input("expense_cost_{$rowNum}");
                    $quantity = $request->input("expense_qty_{$rowNum}");

                    // فقط إذا كان هناك بيانات في الصف
                    if ($expenseType || $description || $unitCost || $quantity) {
                        // تحويل الأرقام العربية إلى أرقام غربية للحساب
                        $unitCost = $this->arabicToWestern($unitCost);
                        $quantity = $this->arabicToWestern($quantity);

                        $unitCost = floatval($unitCost) ?: 0;
                        $quantity = floatval($quantity) ?: 0;

                        Expense::create([
                            'business_model_id' => $businessModel->id,
                            'version' => 1,
                            'expense_type' => $expenseType,
                            'description' => $description,
                            'unit_cost' => $unitCost,
                            'quantity' => $quantity,
                            'currency_code' => $validated['currency'],
                            'display_order' => $displayOrder++,
                        ]);
                    }
                }
            }

            DB::commit();

            // إعادة التوجيه إلى قائمة النماذج مع رسالة نجاح
            return redirect()->route('display-list')
                ->with('success', 'تم إنشاء نموذج العمل التجاري بنجاح! يمكنك عرضه من القائمة أدناه.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء نموذج العمل: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * عرض نموذج الأعمال
     */
    public function show($id)
    {
        $user = Auth::user();

        $businessModel = BusinessModel::with([
            'project',
            'valuePropositions',
            'customerSegments.segmentType',
            'customerRelationships.relationshipType',
            'channels.channelType',
            'keyActivities',
            'keyResources.resourceType',
            'keyPartnerships',
            'revenueStreams.streamType',
            'expenses'
        ])->findOrFail($id);

        // التحقق من أن المستخدم يملك هذا النموذج
        if ($businessModel->project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا النموذج');
        }

        return view('pages.display', compact('businessModel'));
    }

    /**
     * عرض صفحة تعديل نموذج الأعمال
     */
    public function edit($id)
    {
        $user = Auth::user();

        $businessModel = BusinessModel::with([
            'project',
            'valuePropositions',
            'customerSegments.segmentType',
            'customerRelationships.relationshipType',
            'channels.channelType',
            'keyActivities',
            'keyResources.resourceType',
            'keyPartnerships',
            'revenueStreams.streamType',
            'expenses'
        ])->findOrFail($id);

        // التحقق من أن المستخدم يملك هذا النموذج
        if ($businessModel->project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذا النموذج');
        }

        return view('pages.edit-model', compact('businessModel'));
    }

    /**
     * تحديث نموذج الأعمال (إنشاء نسخة جديدة)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // التحقق من صحة البيانات
        $validated = $request->validate([
            'value_proposition' => 'required|string',
            'age_group' => 'nullable|string',
            'region' => 'required|string',
            'target_notes' => 'nullable|string',
            'relationship' => 'required|string',
            'relationship_other' => 'nullable|string',
            'channels' => 'required|array|min:1',
            'channel_other' => 'nullable|string',
            'key_activities' => 'required|string',
            'resources' => 'required|array|min:1',
            'human_details' => 'nullable|string',
            'technical_details' => 'nullable|string',
            'equipment_details' => 'nullable|string',
            'resource_other_details' => 'nullable|string',
            'key_partners' => 'nullable|string',
            'income' => 'required|array|min:1',
            'income_other' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $oldModel = BusinessModel::findOrFail($id);

            // التحقق من أن المستخدم يملك هذا النموذج
            if ($oldModel->project->user_id !== $user->id) {
                abort(403, 'غير مصرح لك بتعديل هذا النموذج');
            }

            // إلغاء تفعيل النموذج القديم
            $oldModel->is_active = false;
            $oldModel->save();

            // إنشاء نموذج جديد بنسخة محدثة
            // إنشاء نموذج جديد بنسخة محدثة - التحقق من أعلى نسخة بما في ذلك المحذوفة
            $maxVersion = BusinessModel::withTrashed()
                ->where('project_id', $oldModel->project_id)
                ->max('version');
            $newVersion = $maxVersion + 1;
            $newModel = BusinessModel::create([
                'project_id' => $oldModel->project_id,
                'version' => $newVersion,
                'currency_code' => $validated['currency'],
                'is_active' => true,
            ]);

            // نسخ جميع المكونات بنفس طريقة generate()
            // 1. القيمة المضافة
            ValueProposition::create([
                'business_model_id' => $newModel->id,
                'version' => $newVersion,
                'content' => $validated['value_proposition'],
            ]);

            // 2. الشريحة المستهدفة
            CustomerSegment::create([
                'business_model_id' => $newModel->id,
                'version' => $newVersion,
                'segment_type_id' => null,
                'age_group' => $validated['age_group'],
                'region' => $validated['region'],
                'notes' => $validated['target_notes'],
            ]);

            // 3. العلاقات مع العملاء
            $relationshipTypeId = null;
            if ($validated['relationship'] !== 'other') {
                $relationshipType = RelationshipType::firstOrCreate(
                    ['code' => $validated['relationship']],
                    ['name' => $validated['relationship']]
                );
                $relationshipTypeId = $relationshipType->id;
            }

            CustomerRelationship::create([
                'business_model_id' => $newModel->id,
                'version' => $newVersion,
                'relationship_type_id' => $relationshipTypeId,
                'details' => $validated['relationship'] === 'other'
                    ? $validated['relationship_other']
                    : null,
            ]);

            // 4. القنوات
            foreach ($validated['channels'] as $channel) {
                if ($channel === 'other') {
                    Channel::create([
                        'business_model_id' => $newModel->id,
                        'version' => $newVersion,
                        'channel_type_id' => null,
                        'details' => $validated['channel_other'],
                    ]);
                } else {
                    $channelType = ChannelType::firstOrCreate(
                        ['code' => $channel],
                        ['name' => $channel]
                    );

                    Channel::create([
                        'business_model_id' => $newModel->id,
                        'version' => $newVersion,
                        'channel_type_id' => $channelType->id,
                        'details' => null,
                    ]);
                }
            }

            // 5. الأنشطة الرئيسية
            KeyActivity::create([
                'business_model_id' => $newModel->id,
                'version' => $newVersion,
                'description' => $validated['key_activities'],
            ]);

            // 6. الموارد الرئيسية
            $resourceMapping = [
                'human' => 'human_details',
                'technical' => 'technical_details',
                'equipment' => 'equipment_details',
                'other' => 'resource_other_details',
            ];

            foreach ($validated['resources'] as $resource) {
                $detailsField = $resourceMapping[$resource] ?? null;
                $details = $detailsField && isset($validated[$detailsField])
                    ? $validated[$detailsField]
                    : null;

                if (empty($details)) {
                    continue;
                }

                if ($resource !== 'other') {
                    $resourceType = ResourceType::firstOrCreate(
                        ['code' => $resource],
                        ['name' => $resource]
                    );

                    KeyResource::create([
                        'business_model_id' => $newModel->id,
                        'version' => $newVersion,
                        'resource_type_id' => $resourceType->id,
                        'details' => $details,
                    ]);
                } else {
                    $resourceType = ResourceType::firstOrCreate(
                        ['code' => 'other'],
                        ['name' => 'أخرى']
                    );

                    KeyResource::create([
                        'business_model_id' => $newModel->id,
                        'version' => $newVersion,
                        'resource_type_id' => $resourceType->id,
                        'details' => $details,
                    ]);
                }
            }

            // 7. الشراكات الرئيسية
            if (!empty($validated['key_partners'])) {
                KeyPartnership::create([
                    'business_model_id' => $newModel->id,
                    'version' => $newVersion,
                    'description' => $validated['key_partners'],
                ]);
            }

            // 8. مصادر الدخل
            foreach ($validated['income'] as $income) {
                if ($income === 'other') {
                    RevenueStream::create([
                        'business_model_id' => $newModel->id,
                        'version' => $newVersion,
                        'stream_type_id' => null,
                        'details' => $validated['income_other'],
                        'currency_code' => $validated['currency'],
                    ]);
                } else {
                    $streamType = RevenueStreamType::firstOrCreate(
                        ['code' => $income],
                        ['name' => $income]
                    );

                    RevenueStream::create([
                        'business_model_id' => $newModel->id,
                        'version' => $newVersion,
                        'stream_type_id' => $streamType->id,
                        'currency_code' => $validated['currency'],
                    ]);
                }
            }

            // 9. هيكل التكلفة
            $displayOrder = 1;
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^expense_type_(\d+)$/', $key, $matches)) {
                    $rowNum = $matches[1];
                    $expenseType = $request->input("expense_type_{$rowNum}");
                    $description = $request->input("expense_desc_{$rowNum}");
                    $unitCost = $request->input("expense_cost_{$rowNum}");
                    $quantity = $request->input("expense_qty_{$rowNum}");

                    if ($expenseType || $description || $unitCost || $quantity) {
                        $unitCost = $this->arabicToWestern($unitCost);
                        $quantity = $this->arabicToWestern($quantity);

                        $unitCost = floatval($unitCost) ?: 0;
                        $quantity = floatval($quantity) ?: 0;

                        Expense::create([
                            'business_model_id' => $newModel->id,
                            'version' => $newVersion,
                            'expense_type' => $expenseType,
                            'description' => $description,
                            'unit_cost' => $unitCost,
                            'quantity' => $quantity,
                            'currency_code' => $validated['currency'],
                            'display_order' => $displayOrder++,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('business-model.show', $newModel->id)
                ->with('success', 'تم تحديث نموذج العمل بنجاح! الإصدار الجديد: ' . $newVersion);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث النموذج: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * تحويل الأرقام العربية إلى أرقام غربية
     */
    private function arabicToWestern($str)
    {
        if (empty($str)) return '0';

        $arabicToWestern = [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '۸' => '8', '۹' => '9'
        ];

        return str_replace(array_keys($arabicToWestern), array_values($arabicToWestern), $str);
    }
}
