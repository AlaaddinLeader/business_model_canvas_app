<?php

namespace App\Http\Controllers\model_business;

use App\Http\Controllers\Controller;
use App\Models\BusinessModel;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with user statistics and recent models
     */
    public function showDashboard()
    {
        $user = Auth::user();

        // Get all user's projects
        $userProjects = Project::where('user_id', $user->id)->pluck('id');

        // Total number of business models (all versions)
        $totalModels = BusinessModel::whereIn('project_id', $userProjects)->count();

        // Business models created this month
        $monthlyModels = BusinessModel::whereIn('project_id', $userProjects)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Active business models (is_active = true)
        $activeModels = BusinessModel::whereIn('project_id', $userProjects)
            ->where('is_active', true)
            ->count();

        // Get recent business models (last 5 active models)
        $recentModels = BusinessModel::with([
            'project',
            'valuePropositions' => function($query) {
                $query->orderBy('version', 'desc')->limit(1);
            }
        ])
            ->whereIn('project_id', $userProjects)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->project->name ?? 'نموذج عمل تجاري',
                    'value_proposition' => $model->valuePropositions->first()->content ?? 'لا يوجد وصف',
                    'version' => $model->version,
                    'created_at' => $model->created_at,
                    'currency' => $model->currency_code,
                ];
            });

        // Additional insights (optional)
        $insights = $this->getInsights($user->id, $userProjects);

        return view('pages.dashboard', compact(
            'totalModels',
            'monthlyModels',
            'activeModels',
            'recentModels',
            'insights'
        ));
    }

    /**
     * Get additional insights for dashboard
     */
    private function getInsights($userId, $projectIds)
    {
        return [
            // Total projects
            'totalProjects' => Project::where('user_id', $userId)->count(),

            // Most recent project
            'latestProject' => Project::where('user_id', $userId)
                ->latest()
                ->first(),

            // Growth rate (comparing this month vs last month)
            'growthRate' => $this->calculateGrowthRate($projectIds),

            // Most used currency
            'mostUsedCurrency' => BusinessModel::whereIn('project_id', $projectIds)
                ->select('currency_code', DB::raw('count(*) as count'))
                ->groupBy('currency_code')
                ->orderBy('count', 'desc')
                ->first()?->currency_code ?? 'USD',

            // Average models per project
            'avgModelsPerProject' => $projectIds->count() > 0
                ? round(BusinessModel::whereIn('project_id', $projectIds)->count() / $projectIds->count(), 1)
                : 0,
        ];
    }

    /**
     * Calculate growth rate comparing current month to previous month
     */
    private function calculateGrowthRate($projectIds)
    {
        $currentMonth = BusinessModel::whereIn('project_id', $projectIds)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonth = BusinessModel::whereIn('project_id', $projectIds)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    /**
     * Get quick stats for API or AJAX requests
     */
    public function getQuickStats()
    {
        $user = Auth::user();
        $userProjects = Project::where('user_id', $user->id)->pluck('id');

        return response()->json([
            'success' => true,
            'data' => [
                'total_models' => BusinessModel::whereIn('project_id', $userProjects)->count(),
                'active_models' => BusinessModel::whereIn('project_id', $userProjects)
                    ->where('is_active', true)
                    ->count(),
                'total_projects' => Project::where('user_id', $user->id)->count(),
                'monthly_models' => BusinessModel::whereIn('project_id', $userProjects)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
            ]
        ]);
    }
}
