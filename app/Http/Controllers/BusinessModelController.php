<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\BusinessModel;
use App\Models\ModelBlock;
use Illuminate\Support\Facades\DB;

class BusinessModelController extends Controller
{
    public function index(){
        return view('data-input');
    }
    /**
     * عرض صفحة الإدخال
     */
    public function showInputForm()
    {
        return view('input');
    }

    /**
     * معالجة البيانات وإنشاء نموذج الأعمال
     */
    public function generateBusinessModel(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_description' => 'required|string',
            'industry' => 'required|string',
            'revenue_method' => 'required|string',
            'additional_notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // 1. إنشاء المشروع
            // مؤقتاً سنستخدم user_id = 1 (يمكنك تغييره لاحقاً عند إضافة نظام تسجيل الدخول)
            $project = Project::create([
                'user_id' => 1, // مؤقت - سيتم استبداله بـ auth()->id() لاحقاً
                'project_name' => $validated['project_name'],
                'project_description' => $validated['project_description'],
                'industry' => $validated['industry'],
                'revenue_method' => $validated['revenue_method'],
                'notes' => $validated['additional_notes'] ?? null
            ]);

            // 2. إنشاء نموذج الأعمال
            $businessModel = BusinessModel::create([
                'project_id' => $project->project_id,
                'version' => 1
            ]);

            // 3. إنشاء عناصر نموذج الأعمال (Business Model Canvas Blocks)
            $blocks = $this->generateModelBlocks($validated);

            foreach ($blocks as $blockName => $blockContent) {
                ModelBlock::create([
                    'model_id' => $businessModel->model_id,
                    'block_name' => $blockName,
                    'block_content' => $blockContent
                ]);
            }

            DB::commit();

            // إعادة التوجيه إلى صفحة العرض مع معرف النموذج
            return redirect()->route('display', ['id' => $businessModel->model_id])
                           ->with('success', 'تم إنشاء نموذج الأعمال بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء النموذج: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج الأعمال
     */
    public function displayBusinessModel($id)
    {
        try {
            // جلب نموذج الأعمال مع العلاقات
            $businessModel = BusinessModel::with(['project', 'blocks'])
                                         ->findOrFail($id);

            // تنظيم البيانات للعرض
            $blocks = $businessModel->blocks->pluck('block_content', 'block_name')->toArray();

            return view('display', [
                'project' => $businessModel->project,
                'businessModel' => $businessModel,
                'blocks' => $blocks
            ]);

        } catch (\Exception $e) {
            return redirect()->route('home')
                           ->with('error', 'لم يتم العثور على النموذج المطلوب');
        }
    }

    /**
     * إنشاء محتوى عناصر نموذج الأعمال
     * يمكنك تخصيص هذه الدالة حسب احتياجاتك
     */
    private function generateModelBlocks($data)
    {
        $industry = $this->getIndustryName($data['industry']);
        $revenueMethod = $this->getRevenueMethodName($data['revenue_method']);

        return [
            'الشرائح_المستهدفة' => $this->generateCustomerSegments($data),
            'عرض_القيمة' => $this->generateValueProposition($data),
            'القنوات' => $this->generateChannels($data),
            'العلاقة_مع_العملاء' => $this->generateCustomerRelationships($data),
            'مصادر_الإيرادات' => $this->generateRevenueStreams($data),
            'الموارد_الرئيسية' => $this->generateKeyResources($data),
            'الأنشطة_الرئيسية' => $this->generateKeyActivities($data),
            'الشركاء_الرئيسيون' => $this->generateKeyPartners($data),
            'هيكل_التكاليف' => $this->generateCostStructure($data)
        ];
    }

    // دوال مساعدة لإنشاء محتوى كل عنصر
    private function generateCustomerSegments($data)
    {
        $segments = [
            'trade' => 'المستهلكون النهائيون، تجار الجملة، تجار التجزئة',
            'technology' => 'الشركات الصغيرة والمتوسطة، المستخدمون الأفراد، المؤسسات الكبيرة',
            'education' => 'الطلاب، المعلمون، المؤسسات التعليمية',
            'agriculture' => 'المزارعون، التجار، المصنعون الزراعيون',
            'health' => 'المرضى، المستشفيات، العيادات',
            'finance' => 'الأفراد، الشركات الصغيرة، المستثمرون',
            'industry' => 'المصانع، الشركات الصناعية، الموردون',
            'tourism' => 'السياح المحليون، السياح الدوليون، وكالات السفر'
        ];

        return $segments[$data['industry']] ?? 'الفئة المستهدفة بناءً على طبيعة المشروع';
    }

    private function generateValueProposition($data)
    {
        return "حل مبتكر في مجال " . $this->getIndustryName($data['industry']) .
               " يوفر قيمة مضافة للعملاء من خلال: " . $data['project_description'];
    }

    private function generateChannels($data)
    {
        return "المنصات الرقمية، الموقع الإلكتروني، وسائل التواصل الاجتماعي، القنوات التقليدية حسب طبيعة المشروع";
    }

    private function generateCustomerRelationships($data)
    {
        return "خدمة عملاء متميزة، دعم فني مستمر، برامج ولاء، تواصل دائم عبر القنوات المختلفة";
    }

    private function generateRevenueStreams($data)
    {
        $methods = [
            'direct_sales' => 'المبيعات المباشرة للمنتجات/الخدمات',
            'subscriptions' => 'نموذج الاشتراكات الشهرية/السنوية',
            'ads' => 'عائدات الإعلانات والترويج',
            'commissions' => 'عمولات على المعاملات',
            'licensing' => 'رسوم الترخيص والاستخدام',
            'other' => 'مصادر دخل متنوعة'
        ];

        return $methods[$data['revenue_method']] ?? 'مصادر الدخل الرئيسية';
    }

    private function generateKeyResources($data)
    {
        return "الموارد البشرية المؤهلة، التقنية والبنية التحتية، رأس المال، العلاقات والشراكات، الملكية الفكرية";
    }

    private function generateKeyActivities($data)
    {
        return "تطوير المنتج/الخدمة، التسويق والمبيعات، إدارة العمليات، خدمة العملاء، البحث والتطوير";
    }

    private function generateKeyPartners($data)
    {
        return "الموردون الرئيسيون، شركاء التوزيع، الشركاء التقنيون، المستشارون والخبراء";
    }

    private function generateCostStructure($data)
    {
        return "تكاليف التشغيل، الرواتب والأجور، التسويق والإعلان، البنية التحتية، التطوير والصيانة";
    }

    // دوال مساعدة للترجمة
    private function getIndustryName($industry)
    {
        $industries = [
            'trade' => 'التجارة',
            'technology' => 'التكنولوجيا',
            'education' => 'التعليم',
            'agriculture' => 'الزراعة',
            'health' => 'الصحة',
            'finance' => 'الخدمات المالية',
            'industry' => 'الصناعة',
            'tourism' => 'السياحة'
        ];

        return $industries[$industry] ?? $industry;
    }

    private function getRevenueMethodName($method)
    {
        $methods = [
            'direct_sales' => 'المبيعات المباشرة',
            'subscriptions' => 'الاشتراكات',
            'ads' => 'الإعلانات',
            'commissions' => 'العمولات',
            'licensing' => 'الترخيص',
            'other' => 'أخرى'
        ];

        return $methods[$method] ?? $method;
    }
}
