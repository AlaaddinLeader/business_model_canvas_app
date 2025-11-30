<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج العمل التجاري - {{ $businessModel->project->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #CBDCEB 0%, #E8DFCA 100%);
            padding: 20px;
            direction: rtl;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #6D94C5;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .header p {
            color: #5a6c7d;
            font-size: 16px;
            font-weight: 600;
        }

        .model-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: auto auto auto;
            gap: 15px;
            margin-bottom: 20px;
        }

        .model-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 2px solid #E8DFCA;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            page-break-inside: avoid; /* Prevent box from breaking across pages */
        }

        .model-box-title {
            font-size: 16px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #6D94C5;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0; /* Title doesn't shrink */
        }

        .model-box-title svg {
            width: 20px;
            height: 20px;
            fill: #6D94C5;
            flex-shrink: 0;
        }

        .model-box-content {
            flex: 1;
            font-size: 13px;
            line-height: 1.8;
            color: #2c3e50;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto; /* Auto hyphenation for long words */
        }

        .model-box-content p {
            margin-bottom: 8px;
            word-break: break-word;
        }

        /* Adjust font size for long content */
        .model-box-content.long-content {
            font-size: 11px;
            line-height: 1.6;
        }

        .model-box-content strong {
            color: #6D94C5;
            font-weight: 700;
        }

        .empty-state {
            color: #95a5a6;
            font-style: italic;
            font-size: 12px;
        }

        /* Grid positioning with flexible heights */
        .box-partners {
            grid-column: 1;
            grid-row: 1 / 3;
            min-height: 300px;
        }

        .box-activities {
            grid-column: 2;
            grid-row: 1;
            min-height: 145px;
        }

        .box-value {
            grid-column: 3;
            grid-row: 1 / 3;
            min-height: 300px;
        }

        .box-relationships {
            grid-column: 4;
            grid-row: 1;
            min-height: 145px;
        }

        .box-segments {
            grid-column: 5;
            grid-row: 1 / 3;
            min-height: 300px;
        }

        .box-resources {
            grid-column: 2;
            grid-row: 2;
            min-height: 145px;
        }

        .box-channels {
            grid-column: 4;
            grid-row: 2;
            min-height: 145px;
        }

        .box-costs {
            grid-column: 1 / 3;
            grid-row: 3;
            min-height: 200px;
        }

        .box-revenue {
            grid-column: 3 / 6;
            grid-row: 3;
            min-height: 200px;
        }

        .expense-item,
        .revenue-item {
            margin-bottom: 10px;
            padding: 8px;
            background: rgba(109, 148, 197, 0.05);
            border-radius: 6px;
            page-break-inside: avoid;
        }

        .expense-item p,
        .revenue-item p {
            margin-bottom: 4px;
        }

        .expense-total {
            margin-top: 12px;
            padding: 10px;
            background: #6D94C5;
            color: white;
            border-radius: 8px;
            text-align: center;
            font-weight: 700;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #E8DFCA;
            color: #7f8c8d;
            font-size: 12px;
        }

        /* Print-specific styles */
        @media print {
            body {
                padding: 10px;
            }

            .container {
                box-shadow: none;
            }

            .model-box {
                page-break-inside: avoid;
            }
        }

        /* Text truncation for extremely long content (optional) */
        .text-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 15; /* Show max 15 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $businessModel->project->name }}</h1>
            <p>الإصدار {{ $businessModel->version }} • {{ $businessModel->created_at->format('Y-m-d') }}</p>
        </div>

        <div class="model-grid">
            <!-- 1. Key Partners -->
            <div class="model-box box-partners">
                <div class="model-box-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 11C17.66 11 18.99 9.66 18.99 8C18.99 6.34 17.66 5 16 5C14.34 5 13 6.34 13 8C13 9.66 14.34 11 16 11ZM8 11C9.66 11 10.99 9.66 10.99 8C10.99 6.34 9.66 5 8 5C6.34 5 5 6.34 5 8C5 9.66 6.34 11 8 11ZM8 13C5.67 13 1 14.17 1 16.5V19H15V16.5C15 14.17 10.33 13 8 13ZM16 13C15.71 13 15.38 13.02 15.03 13.05C16.19 13.89 17 15.02 17 16.5V19H23V16.5C23 14.17 18.33 13 16 13Z"/>
                    </svg>
                    الشركاء الرئيسيون
                </div>
                <div class="model-box-content {{ $businessModel->keyPartnerships->sum(fn($p) => strlen($p->description)) > 300 ? 'long-content' : '' }}">
                    @forelse($businessModel->keyPartnerships as $partnership)
                        <p>• {{ $partnership->description }}</p>
                    @empty
                        <p class="empty-state">لم يتم تحديد شركاء</p>
                    @endforelse
                </div>
            </div>

            <!-- 2. Key Activities -->
            <div class="model-box box-activities">
                <div class="model-box-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7V17.5C2 21.64 5.36 24 12 24C18.64 24 22 21.64 22 17.5V7L12 2Z"/>
                    </svg>
                    الأنشطة الرئيسية
                </div>
                <div class="model-box-content {{ $businessModel->keyActivities->sum(fn($a) => strlen($a->description)) > 200 ? 'long-content' : '' }}">
                    @forelse($businessModel->keyActivities as $activity)
                        <p>• {{ $activity->description }}</p>
                    @empty
                        <p class="empty-state">لم يتم تحديد أنشطة</p>
                    @endforelse
                </div>
            </div>

            <!-- 3. Value Proposition -->
            <div class="model-box box-value">
                <div class="model-box-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2ZM12 14.5L4 10.5V15.5C4 16.88 7.58 19.5 12 19.5C16.42 19.5 20 16.88 20 15.5V10.5L12 14.5Z"/>
                    </svg>
                    عرض القيمة
                </div>
                <div class="model-box-content {{ $businessModel->valuePropositions->sum(fn($v) => strlen($v->content)) > 400 ? 'long-content' : '' }}">
                    @forelse($businessModel->valuePropositions as $proposition)
                        <p>{{ $proposition->content }}</p>
                    @empty
                        <p class="empty-state">لم يتم تحديد</p>
                    @endforelse
                </div>
            </div>

            <!-- 4. Customer Relationships -->
            <div class="model-box box-relationships">
                <div class="model-box-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.5 3C13.6 3 12 5.6 12 5.6C12 5.6 10.4 3 7.5 3C5.3 3 3 4.7 3 7.5C3 11.4 7 14.4 12 18C17 14.4 21 11.4 21 7.5C21 4.7 18.7 3 16.5 3Z"/>
                    </svg>
                    العلاقات مع العملاء
                </div>
                <div class="model-box-content">
                    @forelse($businessModel->customerRelationships as $relationship)
                        @if($relationship->relationshipType)
                            <p><strong>{{ $relationship->relationshipType->name }}</strong></p>
                        @endif
                        @if($relationship->details)
                            <p>{{ $relationship->details }}</p>
                        @endif
                    @empty
                        <p class="empty-state">لم يتم تحديد</p>
                    @endforelse
                </div>
            </div>

            <!-- 5. Customer Segments -->
            <div class="model-box box-segments">
                <div class="model-box-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"/>
                    </svg>
                    شرائح العملاء
                </div>
                <div class="model-box-content">
                    @forelse($businessModel->customerSegments as $segment)
                        @if($segment->age_group)
                            <p><strong>الفئة:</strong> {{ $segment->age_group }}</p>
                        @endif
                        @if($segment->region)
                            <p><strong>المنطقة:</strong> {{ $segment->region }}</p>
                        @endif
                        @if($segment->notes)
                            <p><strong>ملاحظات:</strong> {{ $segment->notes }}</p>
                        @endif
                    @empty
                        <p class="empty-state">لم يتم تحديد</p>
                    @endforelse
                </div>
            </div>

            <!-- 6. Key Resources -->
            <div class="model-box box-resources">
                <div class="model-box-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 6H12L10 4H4C2.9 4 2.01 4.9 2.01 6L2 18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V8C22 6.9 21.1 6 20 6Z"/>
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
                        <p class="empty-state">لم يتم تحديد</p>
                    @endforelse
                </div>
            </div>

            <!-- 7. Channels -->
            <div class="model-box box-channels">
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
                        <p class="empty-state">لم يتم تحديد</p>
                    @endforelse
                </div>
            </div>

            <!-- 8. Cost Structure -->
            <div class="model-box box-costs">
                <div class="model-box-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.8 10.9C9.53 10.31 8.8 9.7 8.8 8.75C8.8 7.66 9.81 6.9 11.5 6.9C13.28 6.9 13.94 7.75 14 9H16.21C16.14 7.28 15.09 5.7 13 5.19V3H10V5.16C8.06 5.58 6.5 6.84 6.5 8.77C6.5 11.08 8.41 12.23 11.2 12.9C13.7 13.5 14.2 14.38 14.2 15.31C14.2 16 13.71 17.1 11.5 17.1C9.44 17.1 8.63 16.18 8.52 15H6.32C6.44 17.19 8.08 18.42 10 18.83V21H13V18.85C14.95 18.48 16.5 17.35 16.5 15.3C16.5 12.46 14.07 11.49 11.8 10.9Z"/>
                    </svg>
                    هيكل التكاليف
                </div>
                <div class="model-box-content">
                    @forelse($businessModel->expenses->sortBy('display_order') as $expense)
                        <div class="expense-item">
                            <p><strong>{{ $expense->expense_type }}</strong></p>
                            @if($expense->description)
                                <p style="font-size: 11px; color: #7f8c8d;">{{ $expense->description }}</p>
                            @endif
                            <p style="font-size: 12px;">
                                {{ $expense->unit_cost }} × {{ $expense->quantity }} =
                                <strong>{{ number_format($expense->unit_cost * $expense->quantity, 2) }} {{ $expense->currency_code }}</strong>
                            </p>
                        </div>
                    @empty
                        <p class="empty-state">لم يتم تحديد تكاليف</p>
                    @endforelse
                    @if($businessModel->expenses->count() > 0)
                        <div class="expense-total">
                            المجموع: {{ number_format($businessModel->expenses->sum(fn($e) => $e->unit_cost * $e->quantity), 2) }} {{ $businessModel->currency_code }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- 9. Revenue Streams -->
            <div class="model-box box-revenue">
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
                                <p><strong>• {{ $stream->streamType->name }}</strong></p>
                            @endif
                            @if($stream->details)
                                <p style="font-size: 12px;">{{ $stream->details }}</p>
                            @endif
                            @if($stream->projected_amount)
                                <p style="font-size: 12px; color: #27ae60;">
                                    المتوقع: {{ number_format($stream->projected_amount, 2) }} {{ $stream->currency_code }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="empty-state">لم يتم تحديد مصادر دخل</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="footer">
            <p>تم إنشاء هذا النموذج باستخدام نظام نموذج العمل التجاري • {{ now()->format('Y-m-d H:i') }}</p>
        </div>
    </div>
</body>
</html>
