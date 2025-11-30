@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('page-title', 'لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <!-- Welcome Section -->
    <section class="welcome-section">
        <h1>مرحباً بك، {{ Auth::user()->name }}</h1>
        <p>قم بإنشاء وإدارة نماذج أعمالك التجارية بسهولة وفعالية. لديك {{ $totalModels }} {{ $totalModels == 1 ? 'نموذج' : 'نماذج' }} حتى الآن.</p>
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
            <div class="stat-value">{{ $totalModels }}</div>
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
            <div class="stat-value">{{ $monthlyModels }}</div>
            @if(isset($insights['growthRate']) && $insights['growthRate'] != 0)
                <div class="stat-badge" style="margin-top: 8px; font-size: 13px; color: {{ $insights['growthRate'] > 0 ? '#27ae60' : '#e74c3c' }};">
                    {{ $insights['growthRate'] > 0 ? '+' : '' }}{{ $insights['growthRate'] }}% عن الشهر الماضي
                </div>
            @endif
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
            <div class="stat-value">{{ $activeModels }}</div>
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

        <a href="{{ route('display-list') }}" class="action-card">
            <div class="action-card-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM17 12H7V14H17V12ZM17 9H7V11H17V9ZM17 15H7V17H17V15Z" fill="white"/>
                </svg>
            </div>
            <h3>عرض جميع النماذج</h3>
            <p>استعرض وأدر جميع نماذج الأعمال التجارية التي قمت بإنشائها مع إمكانية التعديل والحذف</p>
            <div class="action-card-button">عرض النماذج</div>
        </a>
    </div>

    <!-- Recent Models Section -->
    <section class="recent-models">
        <h2>النماذج الأخيرة</h2>

        @if(isset($recentModels) && $recentModels->count() > 0)
            <div class="models-list">
                @foreach($recentModels as $model)
                    <div class="model-item" onclick="window.location.href='{{ route('business-model.show', $model['id']) }}'">
                        <div class="model-info">
                            <div class="model-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L2 7L12 12L22 7L12 2ZM12 14.5L4 10.5V15.5C4 16.88 7.58 19.5 12 19.5C16.42 19.5 20 16.88 20 15.5V10.5L12 14.5Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <h3>{{ $model['name'] }}</h3>
                                <p>{{ Str::limit($model['value_proposition'], 80) }}</p>
                                @if(isset($model['version']))
                                    <span style="font-size: 12px; color: #95a5a6; margin-top: 4px; display: inline-block;">
                                        الإصدار {{ $model['version'] }} • {{ $model['currency'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="model-date">
                            {{ $model['created_at_human'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if($totalModels > 5)
                <div style="text-align: center; margin-top: 24px;">
                    <a href="{{ route('display-list') }}" style="
                        display: inline-block;
                        padding: 12px 24px;
                        background: white;
                        color: #6D94C5;
                        border: 2px solid #E8DFCA;
                        border-radius: 12px;
                        font-weight: 700;
                        transition: all 0.3s ease;
                    " onmouseover="this.style.borderColor='#6D94C5'; this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.borderColor='#E8DFCA'; this.style.transform='translateY(0)'">
                        عرض جميع النماذج ({{ $totalModels }})
                    </a>
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C4.9 2 4.01 2.9 4.01 4L4 20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2ZM16 18H8V16H16V18ZM16 14H8V12H16V14ZM13 9V3.5L18.5 9H13Z" fill="white"/>
                    </svg>
                </div>
                <h3>لا توجد نماذج بعد</h3>
                <p>ابدأ رحلتك في بناء نماذج الأعمال التجارية الناجحة</p>
                <a href="{{ route('data-input') }}">إنشاء نموذج جديد</a>
            </div>
        @endif
    </section>

    <!-- Additional Insights (Optional - can be shown/hidden) -->
    @if(isset($insights) && $totalModels > 0)
        <section class="insights-section" style="
            background: var(--color-surface);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-3xl);
            padding: var(--spacing-2xl);
            margin-top: var(--spacing-2xl);
            box-shadow: var(--shadow-sm);
            border: 2px solid var(--border-color);
        ">
            <h2 style="
                font-size: 22px;
                font-weight: 800;
                color: var(--color-text-primary);
                margin-bottom: var(--spacing-lg);
                display: flex;
                align-items: center;
                gap: 12px;
            ">
                <span style="width: 5px; height: 28px; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); border-radius: 3px;"></span>
                إحصائيات إضافية
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div style="padding: 16px; background: white; border-radius: 12px; border: 2px solid #E8DFCA;">
                    <div style="font-size: 13px; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">إجمالي المشاريع</div>
                    <div style="font-size: 28px; font-weight: 800; color: #2c3e50;">{{ $insights['totalProjects'] }}</div>
                </div>

                <div style="padding: 16px; background: white; border-radius: 12px; border: 2px solid #E8DFCA;">
                    <div style="font-size: 13px; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">متوسط النماذج لكل مشروع</div>
                    <div style="font-size: 28px; font-weight: 800; color: #2c3e50;">{{ $insights['avgModelsPerProject'] }}</div>
                </div>

                <div style="padding: 16px; background: white; border-radius: 12px; border: 2px solid #E8DFCA;">
                    <div style="font-size: 13px; color: #7f8c8d; font-weight: 600; margin-bottom: 8px;">العملة الأكثر استخداماً</div>
                    <div style="font-size: 28px; font-weight: 800; color: #2c3e50;">{{ $insights['mostUsedCurrency'] }}</div>
                </div>
            </div>
        </section>
    @endif
@endsection
