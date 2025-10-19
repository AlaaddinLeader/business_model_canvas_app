<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مُولّد نموذج الأعمال</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Common styles for all pages -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    <!-- Index page specific styles -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>
    <div class="background-shapes">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
        <div class="shape shape3"></div>
    </div>

    <div class="header">
        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 3H5C3.89543 3 3 3.89543 3 5V9C3 10.1046 3.89543 11 5 11H9C10.1046 11 11 10.1046 11 9V5C11 3.89543 10.1046 3 9 3Z"/>
                    <path d="M19 3H15C13.8954 3 13 3.89543 13 5V9C13 10.1046 13.8954 11 15 11H19C20.1046 11 21 10.1046 21 9V5C21 3.89543 20.1046 3 19 3Z"/>
                    <path d="M9 13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9C10.1046 21 11 20.1046 11 19V15C11 13.8954 10.1046 13 9 13Z"/>
                    <path d="M19 13H15C13.8954 13 13 13.8954 13 15V19C13 20.1046 13.8954 21 15 21H19C20.1046 21 21 20.1046 21 19V15C21 13.8954 20.1046 13 19 13Z"/>
                </svg>
            </div>
            <span class="logo-text">مُولّد نموذج الأعمال</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            @auth
                <span style="color: #fff; margin-left: 15px;">مرحباً، {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="bottom-btn" style="background: #dc3545;">تسجيل الخروج</button>
                </form>
            @else
                <button class="bottom-btn" onclick="window.location.href='{{ route('login') }}'">ابدأ الآن</button>
            @endauth
        </div>
    </div>

    <div class="main-content">
        <h1 class="main-title">أنشئ نموذج عملك التجاري بسهولة</h1>
        <p class="main-description">
            ساعد في بناء مشروعك من خلال إنشاء نموذج أعمال احترافي يحتوي على جميع العناصر الأساسية.
            أداة بسيطة وفعالة لرواد الأعمال وأصحاب المشاريع الصغيرة.
        </p>

        <div class="features">
            <div class="feature-box">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 11H7V13H9V11ZM13 11H11V13H13V11ZM17 11H15V13H17V11ZM19 4H18V2H16V4H8V2H6V4H5C3.89 4 3.01 4.9 3.01 6L3 20C3 21.1 3.89 22 5 22H19C20.1 22 21 21.1 21 20V6C21 4.9 20.1 4 19 4ZM19 20H5V9H19V20Z"/>
                    </svg>
                </div>
                <h3 class="feature-title">سهل الاستخدام</h3>
                <p class="feature-text">واجهة بسيطة ومباشرة تناسب الجميع حتى المبتدئين</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM9 17H7V10H9V17ZM13 17H11V7H13V17ZM17 17H15V13H17V17Z"/>
                    </svg>
                </div>
                <h3 class="feature-title">نموذج احترافي</h3>
                <p class="feature-text">احصل على نموذج أعمال متكامل بكل العناصر التسعة</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12V19H5V12H3V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V12H19ZM13 12.67L15.59 10.09L17 11.5L12 16.5L7 11.5L8.41 10.09L11 12.67V3H13V12.67Z"/>
                    </svg>
                </div>
                <h3 class="feature-title">تحميل PDF</h3>
                <p class="feature-text">صدّر نموذجك بصيغة PDF جاهز للطباعة والمشاركة</p>
            </div>
        </div>
    </div>
            {{-- <button class="nav-btn" onclick="window.location.href='{{ route('data-input') }}'">إدخال البيانات</button>
            <button class="nav-btn" onclick="window.location.href='{{ route('display') }}'">عرض النموذج</button> --}}
</body>
</html>
