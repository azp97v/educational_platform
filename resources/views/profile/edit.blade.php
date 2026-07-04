<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الملف الشخصي - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }



        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            padding: 0;
            color: var(--text-primary);
        }

        /* Topbar Styles */
        .topbar {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: var(--card-bg);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        [data-theme="dark"] .topbar {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .topbar-left,
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-wrap {
            flex: 1;
            max-width: 450px;
            position: relative;
        }

        .search-wrap input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(198,117,46,0.25);
            border-radius: 20px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            outline: none;
            transition: var(--transition);
        }

        .search-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 2px var(--gold-light);
        }

        .search-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.03);
            color: #d9deea;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .icon-btn:hover {
            color: var(--gold);
            background: var(--gold-light);
        }

        .g-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--bg);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            color: var(--text-primary);
            transition: all 0.6s ease;
        }

        .g-xp i {
            color: var(--gold);
            font-size: 14px;
        }

        .g-streak {
            transition: all 0.6s ease;
        }

        .g-streak i {
            color: #FF9500;
            font-size: 14px;
            animation: floatFlame 3s ease-in-out infinite;
            transition: all 0.6s ease;
        }

        .g-streak.active i {
            animation: floatFlame 3s ease-in-out infinite, glowPulse 2.5s ease-in-out infinite;
            filter: drop-shadow(0 0 12px rgba(255,149,0,0.6));
        }

        .g-streak.inactive i {
            color: #999;
            animation: none;
            filter: drop-shadow(0 0 4px rgba(153,153,153,0.3));
            opacity: 0.7;
        }

        @keyframes floatFlame {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes glowPulse {
            0%, 100% {
                filter: drop-shadow(0 0 12px rgba(255,149,0,0.6));
            }
            50% {
                filter: drop-shadow(0 0 18px rgba(255,149,0,0.9)) drop-shadow(0 0 25px rgba(255,149,0,0.4));
            }
        }

        .u-av {
            width: 40px;
            height: 40px;
            background: var(--gold);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
        }

        .u-av-img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid var(--gold);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card-bg);
            color: var(--gold);
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 30px;
            transition: var(--transition);
            border: 1px solid var(--gold-light);
            box-shadow: var(--shadow);
        }

        .back-button:hover {
            background: var(--card-bg);
            box-shadow: var(--shadow-hover);
            transform: translateX(-5px);
        }

        .form-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gold-light);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title i {
            color: var(--gold);
            font-size: 32px;
        }

        .header-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 30px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--gold);
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        .section-title i {
            color: var(--gold);
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-size: 14px;
        }

        label i {
            color: var(--gold);
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--gold-light);
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            background: var(--card-bg);
            color: var(--text-primary);
            transition: var(--transition);
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="tel"]::placeholder,
        textarea::placeholder {
            color: var(--text-secondary);
            opacity: 1;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="file"]:focus,
        textarea:focus {
            outline: none;
            border-color: var(--gold);
            background: var(--card-bg);
            color: var(--text-primary);
            box-shadow: 0 0 0 4px var(--gold-light);
            transform: translateY(-2px);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            font-size: 14px;
        }

        .input-hint {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .input-hint i {
            color: var(--gold);
        }

        .avatar-preview-wrapper {
            margin-top: 15px;
        }

        .avatar-preview-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: block;
        }

        .avatar-preview {
            display: flex;
            justify-content: center;
            padding: 20px;
            background: var(--gold-light);
            border-radius: 12px;
            border: 2px dashed var(--gold-light);
            transition: var(--transition);
        }

        .avatar-preview:hover {
            border-color: var(--gold);
            background: var(--gold-light);
        }

        .avatar-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 12px;
            display: block;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }

        .file-input-custom {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }

        .file-input-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: var(--transition);
            user-select: none;
        }

        .file-input-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(198, 117, 46, 0.3);
        }

        .file-input-label i {
            font-size: 16px;
        }

        input[type="file"] {
            display: none;
        }

        .file-name {
            color: var(--text-secondary);
            font-size: 13px;
        }

        .error-message {
            color: #d32f2f;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .error-message i {
            font-size: 14px;
        }

        .form-errors {
            background: linear-gradient(135deg, rgba(211, 47, 47, 0.1), rgba(211, 47, 47, 0.05));
            border: 2px solid rgba(211, 47, 47, 0.2);
            color: #d32f2f;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 25px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-errors ul {
            margin: 0;
            padding-left: 20px;
        }

        .form-errors li {
            margin: 6px 0;
        }

        .success-message {
            background: linear-gradient(135deg, rgba(52, 199, 89, 0.1), rgba(52, 199, 89, 0.05));
            border: 2px solid rgba(52, 199, 89, 0.2);
            color: var(--success);
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        .success-message i {
            font-size: 18px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            user-select: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            box-shadow: 0 10px 25px rgba(198, 117, 46, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(198, 117, 46, 0.4);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .btn-cancel {
            background: var(--gold-light);
            color: var(--gold);
            border: 2px solid var(--gold);
        }

        .btn-cancel:hover {
            background: var(--gold);
            color: white;
        }

        .form-note {
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.05), rgba(33, 150, 243, 0.02));
            border-left: 4px solid #2196F3;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 12px;
            color: #1565C0;
            margin-top: 20px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .form-note i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 25px;
            }

            .header-title {
                font-size: 22px;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }

        /* ===== Unified Theme Override ===== */
        body {
            background: var(--theme-page-bg) !important;
            color: var(--theme-text) !important;
        }

        .topbar {
            background: linear-gradient(90deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%) !important;
            border-bottom: 1px solid var(--theme-border) !important;
            box-shadow: var(--theme-shadow-soft) !important;
        }

        .icon-btn {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px !important;
            border: 1px solid var(--theme-border) !important;
            background: var(--theme-surface-2) !important;
            color: var(--theme-gold) !important;
        }

        .search-wrap input {
            background: var(--theme-surface) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-text) !important;
        }

        .search-wrap input:focus {
            background: var(--theme-surface-2) !important;
            border-color: var(--theme-gold) !important;
        }

        .g-badge {
            background: var(--theme-surface-2) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-gold) !important;
        }

        .form-card,
        .back-button {
            background: var(--theme-surface) !important;
            border: 1px solid var(--theme-border) !important;
        }

        .form-note,
        .avatar-preview,
        .alert,
        .success-message {
            background: var(--theme-surface-2) !important;
            border-color: var(--theme-border) !important;
            color: var(--theme-text-soft) !important;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="file"],
        textarea {
            background: var(--theme-surface-2) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-text) !important;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="file"]:focus,
        textarea:focus {
            border-color: var(--theme-gold) !important;
            box-shadow: 0 0 0 3px var(--theme-gold-soft) !important;
        }

        .header-title,
        .section-title,
        label {
            color: var(--theme-text) !important;
        }

        .header-subtitle,
        .input-hint,
        .file-name {
            color: var(--theme-text-soft) !important;
        }

        @media (max-width: 480px) {
            .form-card { padding: 16px; }
            .header-title { font-size: 18px; }
        }
    </style>
</head>
<body>
    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <a href="{{ route('profile.show') }}" class="icon-btn" title="الرجوع" style="text-decoration: none;">
                <i class="ri-arrow-right-line"></i>
            </a>
            <button class="icon-btn" id="darkBtn" title="الوضع الليلي">
                <i class="ri-moon-line" id="darkIcon"></i>
            </button>
            <button class="icon-btn notification-btn" id="notificationBtn" title="الإشعارات">
                <i class="ri-notification-3-line"></i>
            </button>
            @include('components.notification-bell')
            @if(auth()->user()->role === 'student')
                <div class="g-badge g-xp">
                    <span id="userXP">{{ auth()->user()->points ?? 0 }}</span>
                    <i class="ri-flashlight-fill"></i>
                </div>
            @endif
        </div>

        <div class="search-wrap">
            <input type="text" placeholder="بحث...">
            <i class="ri-search-line search-icon"></i>
        </div>

        <div class="topbar-right">
            @if(auth()->user()->avatar_url)
                <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" alt="{{ auth()->user()->name }}" class="u-av-img">
            @else
                <div class="u-av">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            @endif
        </div>
    </header>

    <div class="container">
        <a href="{{ route('profile.show') }}" class="back-button">
            <i class="ri-arrow-right-line"></i> العودة للملف الشخصي
        </a>

        <div class="form-card">
            <div class="header-title">
                <i class="ri-edit-line"></i> تعديل الملف الشخصي
            </div>
            <div class="header-subtitle">قم بتحديث معلومات حسابك والصورة الشخصية</div>

            @if (session('success'))
                <div class="success-message">
                    <i class="ri-check-circle-line"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="form-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- معلومات شخصية -->
                <div class="section-title">
                    <i class="ri-user-line"></i> المعلومات الشخصية
                </div>

                <div class="form-group">
                    <label for="name">
                        <i class="ri-user-3-line"></i> الاسم الكامل
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    <div class="input-hint">
                        <i class="ri-info-line"></i> الاسم الذي سيظهر على ملفك الشخصي
                    </div>
                    @error('name')
                        <div class="error-message">
                            <i class="ri-close-circle-line"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">
                        <i class="ri-user-star-line"></i> اسم المستخدم
                    </label>
                    <div style="position:relative;">
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="example_user" autocomplete="off" style="padding-left:40px;">
                        <span id="usernameStatus" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:18px;"></span>
                    </div>
                    <div class="input-hint" id="usernameHint">
                        <i class="ri-info-line"></i> يجب أن يكون فريداً — حروف إنجليزية وأرقام و _ فقط
                    </div>
                    @error('username')
                        <div class="error-message">
                            <i class="ri-close-circle-line"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="ri-mail-line"></i> البريد الإلكتروني
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    <div class="input-hint">
                        <i class="ri-info-line"></i> البريد المستخدم في تسجيل الدخول
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="ri-close-circle-line"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">
                        <i class="ri-phone-line"></i> رقم الهاتف
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+966501234567">
                    <div class="input-hint">
                        <i class="ri-info-line"></i> اختياري - رقم هاتف للتواصل
                    </div>
                    @error('phone')
                        <div class="error-message">
                            <i class="ri-close-circle-line"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="bio">
                        <i class="ri-file-text-line"></i> نبذة عني
                    </label>
                    <textarea id="bio" name="bio" placeholder="اكتب نبذة قصيرة عن نفسك...">{{ old('bio', $user->bio) }}</textarea>
                    <div class="input-hint">
                        <i class="ri-info-line"></i> حد أقصى 500 حرف - شارك معلومات عنك واهتماماتك
                    </div>
                    @error('bio')
                        <div class="error-message">
                            <i class="ri-close-circle-line"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- الصورة الشخصية -->
                <div class="section-title">
                    <i class="ri-image-line"></i> الصورة الشخصية
                </div>

                <div class="form-group">
                    <label for="avatar_url">
                        <i class="ri-camera-line"></i> تحديث الصورة
                    </label>

                    <div class="file-input-custom">
                        <label for="avatar_url" class="file-input-label">
                            <i class="ri-upload-cloud-line"></i> اختر صورة
                        </label>
                        <span class="file-name" id="fileName">لم يتم اختيار صورة</span>
                    </div>

                    <input type="file" id="avatar_url" name="avatar_url" accept="image/jpeg,image/png,image/jpg,image/gif">
                    <div class="input-hint">
                        <i class="ri-info-line"></i> صيغ مقبولة: JPEG, PNG, JPG, GIF - الحد الأقصى: 2 ميجابايت
                    </div>

                    @if($user->avatar_url)
                        <div class="avatar-preview-wrapper">
                            <span class="avatar-preview-label">الصورة الحالية:</span>
                            <div class="avatar-preview">
                                <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="صورة شخصية" class="avatar-image" id="currentAvatar">
                            </div>
                            <label style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;font-size:13px;color:var(--theme-danger);cursor:pointer;">
                                <input type="checkbox" name="remove_avatar" value="1" id="removeAvatar">
                                <i class="ri-delete-bin-line"></i> حذف الصورة الحالية
                            </label>
                        </div>
                    @endif

                    <div class="avatar-preview-wrapper" id="previewWrapper" style="display: none;">
                        <span class="avatar-preview-label">معاينة الصورة الجديدة:</span>
                        <div class="avatar-preview">
                            <img id="previewImage" class="avatar-image" alt="معاينة">
                        </div>
                    </div>

                    @error('avatar_url')
                        <div class="error-message">
                            <i class="ri-close-circle-line"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-note">
                    <i class="ri-lightbulb-line"></i>
                    <span>نصيحة: استخدم صورة عالية الجودة بخلفية نظيفة لأفضل مظهر</span>
                </div>

                <!-- الأزرار -->
                <div class="button-group">
                    <button type="submit" class="btn btn-submit">
                        <i class="ri-save-line"></i> حفظ التغييرات
                    </button>
                    <a href="{{ route('profile.show') }}" class="btn btn-cancel">
                        <i class="ri-close-line"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize dark mode from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            // Set default theme for teachers to dark
            let savedTheme = localStorage.getItem('theme');
            if (!savedTheme) {
                savedTheme = '{{ auth()->user()->role === "teacher" ? "dark" : "light" }}';
                localStorage.setItem('theme', savedTheme);
            }
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                updateDarkModeIcon();
            }
            loadSettings();

            document.getElementById('darkBtn')?.addEventListener('click', function() {
                if (typeof toggleDarkMode === 'function') toggleDarkMode();
            });

            document.getElementById('avatar_url')?.addEventListener('change', function() {
                handleFileSelect(this);
            });

            document.getElementById('removeAvatar')?.addEventListener('change', function() {
                handleRemoveAvatar(this);
            });
        });

        function toggleDarkMode() {
            if (typeof window.toggleThemeUniversal === 'function') {
                window.toggleThemeUniversal();
            } else {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
                localStorage.setItem('theme', isDark ? 'light' : 'dark');
            }
            updateDarkModeIcon();
            updateDarkModeToggle();
        }

        function updateDarkModeIcon() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const darkIcon = document.getElementById('darkIcon');
            if (darkIcon) {
                darkIcon.className = isDark ? 'ri-sun-line' : 'ri-moon-line';
            }
        }

        function updateDarkModeToggle() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const darkModeToggle = document.querySelector('.settings-group .setting-item:nth-child(1) .toggle-switch');
            if (darkModeToggle) {
                if (isDark) {
                    darkModeToggle.classList.add('active');
                } else {
                    darkModeToggle.classList.remove('active');
                }
            }
        }

        function toggleSetting(element, setting) {
            element.classList.toggle('active');
            const isActive = element.classList.contains('active');
            localStorage.setItem(`setting_${setting}`, isActive);

            // Handle dark mode separately
            if (setting === 'darkMode') {
                if (isActive) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                }
                updateDarkModeIcon();
            }
        }

        function loadSettings() {
            const darkModeToggle = document.querySelector('.settings-group .setting-item:nth-child(1) .toggle-switch');
            const savedTheme = localStorage.getItem('theme');

            if (savedTheme === 'dark' && darkModeToggle) {
                darkModeToggle.classList.add('active');
            }

            const otherSettings = ['largeText', 'reduceMotion', 'emailNotif', 'classNotif', 'achievementNotif', 'privateProfile', 'twoFactor'];
            otherSettings.forEach(setting => {
                const value = localStorage.getItem(`setting_${setting}`);
                if (value === 'true') {
                    const settingItems = document.querySelectorAll('.setting-item');
                    settingItems.forEach(item => {
                        const toggle = item.querySelector('.toggle-switch');
                        if (toggle && toggle !== darkModeToggle) {
                            toggle.classList.add('active');
                        }
                    });
                }
            });
        }

        function handleFileSelect(input) {
            const fileName = input.files[0]?.name || 'لم يتم اختيار صورة';
            document.getElementById('fileName').textContent = fileName;

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('previewImage');
                    previewImg.src = e.target.result;
                    document.getElementById('previewWrapper').style.display = 'block';
                    if (document.getElementById('currentAvatar')) {
                        document.getElementById('currentAvatar').style.opacity = '0.5';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                document.getElementById('previewWrapper').style.display = 'none';
                if (document.getElementById('currentAvatar')) {
                    document.getElementById('currentAvatar').style.opacity = '1';
                }
            }
        }

        // Username availability check
        (function() {
            const input = document.getElementById('username');
            const status = document.getElementById('usernameStatus');
            const hint = document.getElementById('usernameHint');
            if (!input) return;
            let timer = null;
            input.addEventListener('input', function() {
                clearTimeout(timer);
                const val = this.value.trim();
                if (!val) { status.innerHTML = ''; hint.innerHTML = '<i class="ri-info-line"></i> يجب أن يكون فريداً — حروف إنجليزية وأرقام و _ فقط'; hint.style.color = ''; return; }
                if (!/^[a-zA-Z0-9_]+$/.test(val)) {
                    status.innerHTML = '<i class="ri-close-circle-fill" style="color:var(--theme-danger);"></i>';
                    hint.innerHTML = 'يُسمح فقط بحروف إنجليزية وأرقام و _';
                    hint.style.color = 'var(--theme-danger)';
                    return;
                }
                status.innerHTML = '<i class="ri-loader-4-line" style="color:var(--text-muted);animation:spin 1s linear infinite;"></i>';
                timer = setTimeout(function() {
                    fetch('{{ route("profile.check-username") }}?username=' + encodeURIComponent(val))
                        .then(function(r) { return r.json(); })
                        .then(function(d) {
                            if (d.available !== false) {
                                status.innerHTML = '<i class="ri-checkbox-circle-fill" style="color:var(--theme-success);"></i>';
                                hint.innerHTML = 'اسم المستخدم متوفر ✓';
                                hint.style.color = 'var(--theme-success)';
                            } else {
                                status.innerHTML = '<i class="ri-close-circle-fill" style="color:var(--theme-danger);"></i>';
                                hint.innerHTML = 'اسم المستخدم غير متاح';
                                hint.style.color = 'var(--theme-danger)';
                            }
                        })
                        .catch(function() { status.innerHTML = ''; });
                }, 400);
            });
        })();

        function handleRemoveAvatar(checkbox) {
            var preview = document.getElementById('currentAvatar');
            var wrapper = document.getElementById('previewWrapper');
            if (checkbox.checked) {
                if (preview) preview.style.opacity = '0.3';
                if (wrapper) wrapper.style.display = 'none';
            } else {
                if (preview) preview.style.opacity = '1';
            }
        }

        // Validate file size
        document.getElementById('avatar_url')?.addEventListener('change', function() {
            if (this.files[0]?.size > 2048 * 1024) {
                alert('حجم الصورة يجب أن يكون أقل من 2 ميجابايت');
                this.value = '';
                document.getElementById('fileName').textContent = 'لم يتم اختيار صورة';
            }
        });

        // Character counter for bio
        const bioInput = document.getElementById('bio');
        if (bioInput) {
            bioInput.addEventListener('input', function() {
                const count = this.value.length;
                const maxCount = 500;
                if (count > maxCount) {
                    this.value = this.value.substring(0, maxCount);
                }
            });
        }

        // تحديث حالة الشعلة بناءً على الـ streak
        function updateStreakFlameState() {
            const streakBadge = document.querySelector('.g-streak');
            const streakSpan = document.querySelector('.g-streak span');

            if (streakBadge && streakSpan) {
                const currentStreak = parseInt(streakSpan.textContent) || 0;

                if (currentStreak > 0) {
                    // الـ streak مستمر - شعلة حية مع وميض
                    streakBadge.classList.add('active');
                    streakBadge.classList.remove('inactive');
                } else {
                    // الـ streak منقطع - شعلة رمادية هادئة
                    streakBadge.classList.add('inactive');
                    streakBadge.classList.remove('active');
                }
            }
        }

        // تشغيل التحديث عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            updateStreakFlameState();
        });
    </script>
    @include('components.account-theme-foot')
</body>
</html>




