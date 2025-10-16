<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessModelController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\model_business\DashboardController;

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

// Home route (for authenticated users accessing from app layout)
Route::get('/home', function () {
    return view('pages/index');
})->middleware('auth')->name('home');

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
    // صفحة إدخال البيانات - Data Input Page (News)
    Route::get('/news', [BusinessModelController::class, 'index'])
        ->name('index');

    // صفحة حول - Data Input Form
    Route::get('/data-input', [BusinessModelController::class, 'index'])
        ->name('data-input');

    // معالجة نموذج الإدخال وإنشاء النموذج - Form Submission
    Route::post('/generate', [BusinessModelController::class, 'generateBusinessModel'])
        ->name('generate');

         // معالجة نموذج الإدخال وإنشاء النموذج - Form Submission
        Route::get('/dashboard', [DashboardController::class, 'showDashboard'])
            ->name('dashboard');
    // صفحة عرض نموذج الأعمال - Business Model Display Page
    Route::get('/display', [BusinessModelController::class, 'displayBusinessModel'])
        ->name('display');

    // // Placeholder routes for app layout navigation
    // Route::get('/categories', function () {
    //     return view('pages/index'); // Replace with actual categories view
    // })->name('categories.index');

    // Route::get('/users', function () {
    //     return view('pages/index'); // Replace with actual users view
    // })->name('users.index');

    // Route::get('/settings', function () {
    //     return view('pages/index'); // Replace with actual settings view
    // })->name('settings');
});
