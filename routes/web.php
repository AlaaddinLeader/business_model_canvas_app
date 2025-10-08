<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessModelController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية - Home Page
Route::get('/', function () {
    return view('index');
})->name('index');

// صفحة إدخال البيانات - Data Input Page
Route::get('/input', [BusinessModelController::class, 'showInputForm'])
    ->name('input');

// معالجة نموذج الإدخال وإنشاء النموذج - Form Submission
Route::post('/generate', [BusinessModelController::class, 'generateBusinessModel'])
    ->name('generate');

// صفحة عرض نموذج الأعمال - Business Model Display Page
Route::get('/display/{id}', [BusinessModelController::class, 'displayBusinessModel'])
    ->name('display');

// صفحة حول - data intput Page
Route::get('/data-input', [BusinessModelController::class, 'index'])
    ->name('data-input');
