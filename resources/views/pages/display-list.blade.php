@extends('layouts.app')

@section('title', 'قائمة النماذج - نماذج الأعمال')

@section('page-title', 'قائمة النماذج')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/display-list.css') }}">
@endpush

@section('content')
<div class="dashboard-header">
    <div>
        <h1>نماذج الأعمال الخاصة بك</h1>
        <p>إدارة ومراجعة جميع نماذج الأعمال التجارية</p>
    </div>
    <a href="{{ route('data-input') }}" class="btn-create-new">
        + إنشاء نموذج جديد
    </a>
</div>

@if($businessModels->count() > 0)
    @php
        // Group models by project
        $groupedModels = $businessModels->groupBy('project_id');
    @endphp

    <div class="models-grid">
        @foreach($groupedModels as $projectId => $models)
            @php
                $activeModel = $models->where('is_active', true)->first() ?? $models->first();
                $versionsCount = $models->count();
            @endphp

            <div class="model-card {{ $activeModel->is_active ? 'active' : '' }}">
                <div class="model-card-header">
                    <div>
                        <h3 class="model-title">{{ $activeModel->project->name }}</h3>
                        @if($versionsCount > 1)
                            <p style="font-size: 0.85rem; color: #666; margin: 5px 0 0 0;">
                                {{ $versionsCount }} نسخة متاحة
                            </p>
                        @endif
                    </div>
                    <span class="model-version {{ $activeModel->is_active ? 'active' : '' }}">
                        الإصدار {{ $activeModel->version }}
                    </span>
                </div>

                <div class="model-info">
                    <div class="model-info-item">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                        <span>تم الإنشاء: {{ $activeModel->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="model-info-item">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span>{{ $activeModel->is_active ? 'نشط' : 'غير نشط' }}</span>
                    </div>
                    <div class="model-info-item">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                        <span>العملة: {{ $activeModel->currency_code }}</span>
                    </div>
                </div>

                <div class="model-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ $activeModel->valuePropositions->count() }}</span>
                        <span class="stat-label">القيمة المضافة</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $activeModel->expenses->count() }}</span>
                        <span class="stat-label">التكاليف</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $activeModel->revenueStreams->count() }}</span>
                        <span class="stat-label">الإيرادات</span>
                    </div>
                </div>

                <!-- Show all versions if more than one -->
                @if($versionsCount > 1)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                        <details>
                            <summary style="cursor: pointer; font-weight: bold; color: #667eea; font-size: 0.9rem;">
                                إدارة النسخ ({{ $versionsCount }})
                            </summary>

                            <!-- Form for bulk delete -->
                            <form id="bulkDeleteForm_{{ $projectId }}" action="{{ route('versions.delete-selected') }}" method="POST" onsubmit="return confirmBulkDelete({{ $projectId }})">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="project_id" value="{{ $projectId }}">

                                <div style="margin-top: 10px; display: flex; flex-direction: column; gap: 8px;">
                                    @foreach($models->sortByDesc('version') as $versionModel)
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: {{ $versionModel->is_active ? '#f1f8f4' : '#f8f9fa' }}; border-radius: 4px; border: 1px solid {{ $versionModel->is_active ? '#4caf50' : '#e0e0e0' }};">
                                            <label style="display: flex; align-items: center; gap: 8px; flex: 1; cursor: pointer;">
                                                <input type="checkbox" name="version_ids[]" value="{{ $versionModel->id }}" class="version-checkbox" data-project="{{ $projectId }}">
                                                <div style="flex: 1;">
                                                    <strong>الإصدار {{ $versionModel->version }}</strong>
                                                    @if($versionModel->is_active)
                                                        <span style="color: #4caf50; font-size: 0.8rem;"> (نشط)</span>
                                                    @endif
                                                    <br>
                                                    <span style="font-size: 0.75rem; color: #666;">{{ $versionModel->created_at->format('Y-m-d H:i') }}</span>
                                                </div>
                                            </label>
                                            <a href="{{ route('business-model.show', $versionModel->id) }}" style="background: #667eea; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; text-decoration: none; margin: 0 5px;">
                                                عرض
                                            </a>
                                        </div>
                                    @endforeach
                                </div>

                                <div style="margin-top: 10px; display: flex; gap: 8px;">
                                    <button type="button" onclick="selectAllVersions({{ $projectId }})" style="flex: 1; background: #667eea; color: white; border: none; padding: 8px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                        تحديد الكل
                                    </button>
                                    <button type="button" onclick="deselectAllVersions({{ $projectId }})" style="flex: 1; background: #999; color: white; border: none; padding: 8px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                        إلغاء التحديد
                                    </button>
                                    <button type="submit" style="flex: 1; background: #f44336; color: white; border: none; padding: 8px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                        حذف المحدد
                                    </button>
                                </div>
                            </form>
                        </details>
                    </div>
                @endif

                <div class="model-actions">
                    <a href="{{ route('business-model.show', $activeModel->id) }}" class="btn-view">
                        عرض النسخة النشطة
                    </a>
                    @if($versionsCount > 1)
                        <form action="{{ route('project.delete-all', $projectId) }}" method="POST" style="flex: 1;" onsubmit="return confirm('هل أنت متأكد من حذف المشروع وجميع نسخه ({{ $versionsCount }} نسخة)؟\n\nسيتم حذف:\n- جميع الإصدارات\n- المشروع بالكامل\n\nهذا الإجراء لا يمكن التراجع عنه!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" style="width: 100%;">حذف المشروع بالكامل</button>
                        </form>
                    @else
                        <form action="{{ route('business-model.delete', $activeModel->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('هل أنت متأكد من حذف هذا النموذج؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" style="width: 100%;">حذف</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function selectAllVersions(projectId) {
            document.querySelectorAll(`input.version-checkbox[data-project="${projectId}"]`).forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAllVersions(projectId) {
            document.querySelectorAll(`input.version-checkbox[data-project="${projectId}"]`).forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        function confirmBulkDelete(projectId) {
            const checkedBoxes = document.querySelectorAll(`input.version-checkbox[data-project="${projectId}"]:checked`);

            if (checkedBoxes.length === 0) {
                alert('الرجاء تحديد نسخة واحدة على الأقل للحذف');
                return false;
            }

            const versionNumbers = Array.from(checkedBoxes).map(cb => {
                const label = cb.closest('label').textContent;
                return label.match(/الإصدار (\d+)/)[1];
            }).join(', ');

            return confirm(`هل أنت متأكد من حذف النسخ المحددة؟\n\nسيتم حذف الإصدارات: ${versionNumbers}\n\nملاحظة: إذا تم حذف النسخة النشطة، سيتم تفعيل أحدث نسخة متبقية تلقائياً.`);
        }
    </script>
@else
    <div class="empty-state">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
        </svg>
        <h2>لا توجد نماذج أعمال بعد</h2>
        <p>ابدأ بإنشاء أول نموذج عمل تجاري لك</p>
        <a href="{{ route('data-input') }}" class="btn-create-new">
            إنشاء نموذج جديد
        </a>
    </div>
@endif
@endsection
