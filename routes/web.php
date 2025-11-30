<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessModelController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\model_business\DashboardController;
use App\Http\Controllers\model_business\DisplayListController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية - Home Page
Route::get('/', function () {
    return view('pages/index');
})->name('index');

// App Layout route (for authenticated users accessing from app layout)
Route::get('/app', function () {
    return view('layouts.app');
})->middleware('auth')->name('app');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Guest only routes (for users not logged in)
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Register routes
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Google OAuth routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Logout route (for authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // صفحة لوحة التحكم - Dashboard
     Route::get('/dashboard', [DashboardController::class, 'showDashboard'])
        ->name('dashboard');

    // Optional: API endpoint for quick stats (if you want to refresh stats without page reload)
    Route::get('/api/dashboard/quick-stats', [DashboardController::class, 'getQuickStats'])
        ->name('dashboard.quick-stats');

    // صفحة قائمة النماذج - Display List

    Route::get('/display-list', [DisplayListController::class, 'showModelList'])
        ->name('display-list');

    // صفحة إدخال البيانات - Data Input Form
    Route::get('/data-input', [BusinessModelController::class, 'index'])
        ->name('data-input');

    // معالجة نموذج الإدخال وإنشاء النموذج - Form Submission
    Route::post('/generate', [BusinessModelController::class, 'generate'])
        ->name('generate');

    // صفحة عرض نموذج الأعمال - Business Model Display Page
    Route::get('/business-model/{id}', [BusinessModelController::class, 'show'])
        ->name('business-model.show');

    // حذف نموذج الأعمال - Delete Business Model
    Route::delete('/business-model/{id}', [DisplayListController::class, 'deleteBusinessModel'])
        ->name('business-model.delete');

    // Alternative route for display (backward compatibility)
    Route::get('/display/{id?}', function($id = null) {
        if (!$id) {
            // Redirect to latest business model or data input
            $userId = Auth::id();
            $latestModel = \App\Models\BusinessModel::where('is_active', true)
                ->whereHas('project', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->latest()
                ->first();

            if ($latestModel) {
                return redirect()->route('business-model.show', $latestModel->id);
            }

            return redirect()->route('data-input');
        }

        return redirect()->route('business-model.show', $id);
            })->name('business-model.display');
        // صفحة عرض نموذج الأعمال - Business Model Display Page
        Route::get('/business-model/{id}', [BusinessModelController::class, 'show'])
            ->name('business-model.show');

        // صفحة تعديل نموذج الأعمال - Edit Business Model
        Route::get('/business-model/{id}/edit', [BusinessModelController::class, 'edit'])
            ->name('business-model.edit');

        // تحديث نموذج الأعمال - Update Business Model
        Route::put('/business-model/{id}', [BusinessModelController::class, 'update'])
            ->name('business-model.update');
        Route::delete('/project/{projectId}/delete-all', [DisplayListController::class, 'deleteAllVersions'])
            ->name('project.delete-all');
            // حذف نسخ محددة من النماذج - Delete Selected Versions
            Route::delete('/versions/delete-selected', [DisplayListController::class, 'deleteSelectedVersions'])
                ->name('versions.delete-selected');
    });
