@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('page-title', 'لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <!-- Welcome Section -->
    <section class="welcome-section">
        <h1>مرحباً بك في نظام نموذج العمل التجاري</h1>
        <p>قم بإنشاء وإدارة نماذج أعمالك التجارية بسهولة وفعالية. ابدأ الآن بإنشاء نموذج جديد أو استعرض النماذج السابقة.</p>
    </section>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM9 17H7V10H9V17ZM13 17H11V7H13V17ZM17 17H15V13H17V17Z" fill="white"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">إجمالي النماذج</div>
            <div class="stat-value">{{ $totalModels ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 4H5C3.89 4 3.01 4.9 3.01 6L3 18C3 19.1 3.89 20 5 20H19C20.1 20 21 19.1 21 18V6C21 4.9 20.1 4 19 4ZM19 18H5V10H19V18ZM5 8V6H19V8H5ZM9 14H7V12H9V14ZM13 14H11V12H13V14ZM17 14H15V12H17V14Z" fill="white"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">النماذج هذا الشهر</div>
            <div class="stat-value">{{ $monthlyModels ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" fill="white"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">النماذج النشطة</div>
            <div class="stat-value">{{ $activeModels ?? 0 }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('data-input') }}" class="action-card">
            <div class="action-card-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z" fill="white"/>
                </svg>
            </div>
            <h3>إنشاء نموذج جديد</h3>
            <p>ابدأ بإنشاء نموذج عمل تجاري جديد من خلال إدخال البيانات والمعلومات المطلوبة</p>
            <div class="action-card-button">إنشاء الآن</div>
        </a>

        <a href="{{ route('display') }}" class="action-card">
            <div class="action-card-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM17 12H7V14H17V12ZM17 9H7V11H17V9ZM17 15H7V17H17V15Z" fill="white"/>
                </svg>
            </div>
            <h3>عرض النماذج</h3>
            <p>استعرض جميع نماذج الأعمال التجارية التي قمت بإنشائها مسبقاً</p>
            <div class="action-card-button">عرض النماذج</div>
        </a>
    </div>

    <!-- Recent Models Section -->
    <section class="recent-models">
        <h2>النماذج الأخيرة</h2>

        @if(isset($recentModels) && count($recentModels) > 0)
            <div class="models-list">
                @foreach($recentModels as $model)
                    <div class="model-item" onclick="window.location.href='{{ route('model.show', $model->id) }}'">
                        <div class="model-info">
                            <div class="model-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L2 7L12 12L22 7L12 2ZM12 14.5L4 10.5V15.5C4 16.88 7.58 19.5 12 19.5C16.42 19.5 20 16.88 20 15.5V10.5L12 14.5Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3>{{ $model->name ?? 'نموذج عمل تجاري' }}</h3>
                                <p>{{ Str::limit($model->value_proposition ?? 'لا يوجد وصف', 80) }}</p>
                            </div>
                        </div>
                        <div class="model-date">
                            {{ $model->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C4.9 2 4.01 2.9 4.01 4L4 20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM16 18H8V16H16V18ZM16 14H8V12H16V14ZM13 9V3.5L18.5 9H13Z"/>
                    </svg>
                </div>
                <h3>لا توجد نماذج بعد</h3>
                <p>ابدأ بإنشاء نموذج العمل التجاري الأول الخاص بك</p>
                <a href="{{ route('data-input') }}">إنشاء نموذج جديد</a>
            </div>
        @endif
    </section>
@endsection
