<?php

namespace App\Http\Controllers\model_business;

use App\Http\Controllers\Controller;
use App\Models\BusinessModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DisplayListController extends Controller
{
     /**
     * عرض لوحة التحكم مع جميع نماذج الأعمال للمستخدم
     */
    public function showModelList()
    {
        $user = Auth::user();

        // الحصول على جميع نماذج الأعمال للمستخدم مع العلاقات
        $businessModels = BusinessModel::with([
            'project',
            'valuePropositions',
            'customerSegments',
            'customerRelationships',
            'channels',
            'keyActivities',
            'keyResources',
            'keyPartnerships',
            'revenueStreams',
            'expenses'
        ])
        ->whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view('pages.display-list', compact('businessModels'));
    }

    /**
     * حذف نموذج أعمال
     */
    public function deleteBusinessModel($id)
    {
        $user = Auth::user();

        $businessModel = BusinessModel::findOrFail($id);

        // التحقق من أن المستخدم يملك هذا النموذج
        if ($businessModel->project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بحذف هذا النموذج');
        }

        // حذف النموذج (soft delete)
        $businessModel->delete();

        // إذا كان النموذج المحذوف نشطاً، تفعيل أحدث نسخة متبقية من نفس المشروع
        if ($businessModel->is_active) {
            $latestRemainingVersion = BusinessModel::where('project_id', $businessModel->project_id)
                ->whereNull('deleted_at')
                ->orderBy('version', 'desc')
                ->first();

            if ($latestRemainingVersion) {
                $latestRemainingVersion->is_active = true;
                $latestRemainingVersion->save();
            }
        }

        return redirect()->route('display-list')
            ->with('success', 'تم حذف نموذج الأعمال بنجاح');
    }

    /**
     * حذف جميع نسخ المشروع
     */
    public function deleteAllVersions($projectId)
    {
        $user = Auth::user();

        // التحقق من أن المستخدم يملك هذا المشروع
        $project = \App\Models\Project::findOrFail($projectId);

        if ($project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بحذف هذا المشروع');
        }

        // حذف جميع نماذج الأعمال المرتبطة بهذا المشروع
        BusinessModel::where('project_id', $projectId)->delete();

        // حذف المشروع نفسه
        $project->delete();

        return redirect()->route('display-list')
            ->with('success', 'تم حذف المشروع وجميع نسخه بنجاح');
    }

    /**
     * حذف نسخ محددة من النماذج
     */
    public function deleteSelectedVersions(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'version_ids' => 'required|array|min:1',
            'version_ids.*' => 'exists:business_models,id'
        ]);

        $projectId = $validated['project_id'];
        $versionIds = $validated['version_ids'];

        // التحقق من أن المستخدم يملك المشروع
        $project = \App\Models\Project::findOrFail($projectId);
        if ($project->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بحذف هذه النماذج');
        }

        // التحقق من أن جميع النماذج المحددة تنتمي لنفس المشروع
        $modelsToDelete = BusinessModel::whereIn('id', $versionIds)
            ->where('project_id', $projectId)
            ->get();

        if ($modelsToDelete->count() !== count($versionIds)) {
            return back()->withErrors(['error' => 'بعض النماذج المحددة غير صالحة']);
        }

        // التحقق من وجود نموذج نشط ضمن المحذوفات
        $hasActiveModel = $modelsToDelete->contains('is_active', true);

        // حذف النماذج المحددة
        BusinessModel::whereIn('id', $versionIds)->delete();

        // إذا تم حذف النموذج النشط، تفعيل أحدث نسخة متبقية
        if ($hasActiveModel) {
            $latestRemainingVersion = BusinessModel::where('project_id', $projectId)
                ->whereNull('deleted_at')
                ->orderBy('version', 'desc')
                ->first();

            if ($latestRemainingVersion) {
                $latestRemainingVersion->is_active = true;
                $latestRemainingVersion->save();
            }
        }

        return redirect()->route('display-list')
            ->with('success', 'تم حذف النسخ المحددة بنجاح (' . count($versionIds) . ' نسخة)');
    }
}
