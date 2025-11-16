@extends('layouts.app')

@section('title', 'نموذج الأعمال التجاري')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/display.css') }}">
@endpush

@section('content')

{{-- <h1 class="page-title">نموذج عملك التجاري</h1>
<p class="page-subtitle">قم بمراجعة وتعديل نموذج الأعمال الخاص بك</p> --}}
<!-- Version Navigation Bar -->
@php
    $allVersions = \App\Models\BusinessModel::where('project_id', $businessModel->project_id)
        ->orderBy('version', 'desc')
        ->get();
    $hasMultipleVersions = $allVersions->count() > 1;
@endphp

@if($hasMultipleVersions)
<div class="version-navigation">
    <div class="version-nav-content">
        <div class="version-info">
            <h3>الإصدارات المتاحة ({{ $allVersions->count() }})</h3>
            <p>الإصدار الحالي: {{ $businessModel->version }}</p>
        </div>

        <div class="version-badges">
            @foreach($allVersions as $version)
                @if($version->id == $businessModel->id)
                    <span class="version-badge active">
                        الإصدار {{ $version->version }}
                        @if($version->is_active)
                            <span class="active-star">★</span>
                        @endif
                    </span>
                @else
                    <a href="{{ route('business-model.show', $version->id) }}" class="version-badge">
                        الإصدار {{ $version->version }}
                        @if($version->is_active)
                            <span class="active-star">★</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="model-grid">
    <!-- 1. الشركاء الرئيسيون -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 11C17.66 11 18.99 9.66 18.99 8C18.99 6.34 17.66 5 16 5C14.34 5 13 6.34 13 8C13 9.66 14.34 11 16 11ZM8 11C9.66 11 10.99 9.66 10.99 8C10.99 6.34 9.66 5 8 5C6.34 5 5 6.34 5 8C5 9.66 6.34 11 8 11ZM8 13C5.67 13 1 14.17 1 16.5V19H15V16.5C15 14.17 10.33 13 8 13ZM16 13C15.71 13 15.38 13.02 15.03 13.05C16.19 13.89 17 15.02 17 16.5V19H23V16.5C23 14.17 18.33 13 16 13Z"/>
            </svg>
            الشركاء الرئيسيون
        </div>
        <div class="model-box-content">
            @forelse($businessModel->keyPartnerships as $partnership)
                <p>{{ $partnership->description }}</p>
            @empty
                <p class="empty-state">لم يتم تحديد شركاء رئيسيين</p>
            @endforelse
        </div>
    </div>

    <!-- 2. الأنشطة الرئيسية -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7V17.5C2 21.64 5.36 24 12 24C18.64 24 22 21.64 22 17.5V7L12 2ZM12 11.99H19C18.47 15.11 15.72 17.5 12 17.5C8.28 17.5 5.53 15.11 5 11.99H12V6.3L16.74 9L12 11.69V11.99Z"/>
            </svg>
            الأنشطة الرئيسية
        </div>
        <div class="model-box-content">
            @forelse($businessModel->keyActivities as $activity)
                <p>{{ $activity->description }}</p>
            @empty
                <p class="empty-state">لم يتم تحديد أنشطة رئيسية</p>
            @endforelse
        </div>
    </div>

    <!-- 3. عرض القيمة -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2ZM12 14.5L4 10.5V15.5C4 16.88 7.58 19.5 12 19.5C16.42 19.5 20 16.88 20 15.5V10.5L12 14.5Z"/>
            </svg>
            عرض القيمة
        </div>
        <div class="model-box-content">
            @forelse($businessModel->valuePropositions as $proposition)
                <p>{{ $proposition->content }}</p>
            @empty
                <p class="empty-state">لم يتم تحديد عرض قيمة</p>
            @endforelse
        </div>
    </div>

    <!-- 4. العلاقات مع العملاء -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.5 3C13.6 3 12 5.6 12 5.6C12 5.6 10.4 3 7.5 3C5.3 3 3 4.7 3 7.5C3 11.4 7 14.4 12 18C17 14.4 21 11.4 21 7.5C21 4.7 18.7 3 16.5 3Z"/>
            </svg>
            العلاقات مع العملاء
        </div>
        <div class="model-box-content">
            @forelse($businessModel->customerRelationships as $relationship)
                @if($relationship->relationshipType)
                    <p><strong>النوع:</strong> {{ $relationship->relationshipType->name }}</p>
                @endif
                @if($relationship->details)
                    <p>{{ $relationship->details }}</p>
                @endif
            @empty
                <p class="empty-state">لم يتم تحديد نوع العلاقة مع العملاء</p>
            @endforelse
        </div>
    </div>

    <!-- 5. شرائح العملاء -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"/>
            </svg>
            شرائح العملاء
        </div>
        <div class="model-box-content">
            @forelse($businessModel->customerSegments as $segment)
                @if($segment->age_group)
                    <p><strong>الفئة العمرية:</strong> {{ $segment->age_group }}</p>
                @endif
                @if($segment->region)
                    <p><strong>المنطقة:</strong> {{ $segment->region }}</p>
                @endif
                @if($segment->notes)
                    <p><strong>ملاحظات:</strong> {{ $segment->notes }}</p>
                @endif
            @empty
                <p class="empty-state">لم يتم تحديد شرائح العملاء</p>
            @endforelse
        </div>
    </div>

    <!-- 6. الموارد الرئيسية -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 6H12L10 4H4C2.9 4 2.01 4.9 2.01 6L2 18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V8C22 6.9 21.1 6 20 6ZM20 18H4V8H20V18Z"/>
            </svg>
            الموارد الرئيسية
        </div>
        <div class="model-box-content">
            @forelse($businessModel->keyResources as $resource)
                @if($resource->resourceType)
                    <p><strong>{{ $resource->resourceType->name }}:</strong></p>
                @endif
                @if($resource->details)
                    <p>{{ $resource->details }}</p>
                @endif
            @empty
                <p class="empty-state">لم يتم تحديد موارد رئيسية</p>
            @endforelse
        </div>
    </div>

    <!-- 7. القنوات -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4C7.58 4 4.01 7.58 4.01 12C4.01 16.42 7.58 20 12 20C15.73 20 18.84 17.45 19.73 14H17.65C16.83 16.33 14.61 18 12 18C8.69 18 6 15.31 6 12C6 8.69 8.69 6 12 6C13.66 6 15.14 6.69 16.22 7.78L13 11H20V4L17.65 6.35Z"/>
            </svg>
            القنوات
        </div>
        <div class="model-box-content">
            @forelse($businessModel->channels as $channel)
                @if($channel->channelType)
                    <p>• {{ $channel->channelType->name }}</p>
                @endif
                @if($channel->details)
                    <p>• {{ $channel->details }}</p>
                @endif
            @empty
                <p class="empty-state">لم يتم تحديد قنوات</p>
            @endforelse
        </div>
    </div>

    <!-- 8. هيكل التكاليف -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.8 10.9C9.53 10.31 8.8 9.7 8.8 8.75C8.8 7.66 9.81 6.9 11.5 6.9C13.28 6.9 13.94 7.75 14 9H16.21C16.14 7.28 15.09 5.7 13 5.19V3H10V5.16C8.06 5.58 6.5 6.84 6.5 8.77C6.5 11.08 8.41 12.23 11.2 12.9C13.7 13.5 14.2 14.38 14.2 15.31C14.2 16 13.71 17.1 11.5 17.1C9.44 17.1 8.63 16.18 8.52 15H6.32C6.44 17.19 8.08 18.42 10 18.83V21H13V18.85C14.95 18.48 16.5 17.35 16.5 15.3C16.5 12.46 14.07 11.49 11.8 10.9Z"/>
            </svg>
            هيكل التكاليف
        </div>
        <div class="model-box-content">
            @forelse($businessModel->expenses->sortBy('display_order') as $expense)
                <div class="expense-item">
                    @if($expense->expense_type)
                        <p><strong>{{ $expense->expense_type }}</strong></p>
                    @endif
                    @if($expense->description)
                        <p class="expense-description">{{ $expense->description }}</p>
                    @endif
                    <p class="expense-calculation">
                        التكلفة: {{ $expense->unit_cost }} {{ $expense->currency_code }} ×
                        الكمية: {{ $expense->quantity }} =
                        <strong>{{ number_format($expense->unit_cost * $expense->quantity, 2) }} {{ $expense->currency_code }}</strong>
                    </p>
                </div>
            @empty
                <p class="empty-state">لم يتم تحديد تكاليف</p>
            @endforelse

            @if($businessModel->expenses->count() > 0)
                <div class="expense-total">
                    المجموع الكلي: {{ number_format($businessModel->getTotalExpenses(), 2) }} {{ $businessModel->currency_code }}
                </div>
            @endif
        </div>
    </div>

    <!-- 9. مصادر الإيرادات -->
    <div class="model-box">
        <div class="model-box-title">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13.41 18.09V20H10.74V18.07C9.03 17.71 7.58 16.61 7.47 14.67H9.43C9.53 15.72 10.25 16.54 12.08 16.54C14.04 16.54 14.48 15.56 14.48 14.95C14.48 14.12 14.04 13.34 11.81 12.81C9.33 12.21 7.63 11.19 7.63 9.14C7.63 7.42 8.95 6.3 10.74 5.93V4H13.41V5.95C15.27 6.4 16.2 7.81 16.26 9.38H14.3C14.25 8.34 13.66 7.44 12.08 7.44C10.58 7.44 9.58 8.16 9.58 9.11C9.58 9.94 10.25 10.5 12.38 11.03C14.51 11.56 16.43 12.42 16.43 14.92C16.43 16.37 15.4 17.65 13.41 18.09Z"/>
            </svg>
            مصادر الإيرادات
        </div>
        <div class="model-box-content">
            @forelse($businessModel->revenueStreams as $stream)
                <div class="revenue-item">
                    @if($stream->streamType)
                        <p>• {{ $stream->streamType->name }}</p>
                    @endif
                    @if($stream->details)
                        <p class="revenue-details">{{ $stream->details }}</p>
                    @endif
                    @if($stream->projected_amount)
                        <p class="revenue-amount">المبلغ المتوقع: {{ number_format($stream->projected_amount, 2) }} {{ $stream->currency_code }}</p>
                    @endif
                </div>
            @empty
                <p class="empty-state">لم يتم تحديد مصادر إيرادات</p>
            @endforelse
        </div>
    </div>
</div>

<div class="action-buttons">
    <button class="btn btn-edit" onclick="window.location.href='{{ route('display-list') }}'">
        العودة إلى القائمة
    </button>
    <button class="btn btn-edit" onclick="window.location.href='{{ route('business-model.edit', $businessModel->id) }}'">
        تحديث النموذج
    </button>
    <button class="btn btn-download" onclick="window.print()">
        تحميل كـ PDF
    </button>
</div>
@endsection
