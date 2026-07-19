<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معرض القوالب - {{ $student->name }}</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: radial-gradient(circle at top left, rgba(198,166,117,0.18), transparent 22%),
                        linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-surface-2) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 30px;
        }
        .container { max-width: 1200px; margin: 0 auto; }

        /* ── Top bar with back link ── */
        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }
        .back-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 20px; border-radius: 12px;
            background: var(--theme-surface); border: 1px solid var(--theme-border);
            color: var(--text-secondary); font-size: 13px; font-weight: 600;
            text-decoration: none; transition: 0.25s;
        }
        .back-btn:hover { background: var(--theme-gold-soft); color: var(--text-primary); border-color: var(--theme-gold-soft); }
        .back-btn i { font-size: 16px; }

        .page-title { text-align: center; margin-bottom: 24px; }
        .page-title h1 { font-size: 28px; font-weight: 800; color: var(--theme-gold); }
        .page-title p { color: var(--text-secondary); font-size: 14px; margin-top: 6px; }
        .page-title .badge {
            display: inline-block; padding: 4px 14px; border-radius: 999px;
            background: var(--theme-gold-soft); color: var(--theme-gold);
            font-size: 16px; font-weight: 700; margin-top: 4px;
        }

        /* ── Completion warning banner ── */
        .warning-banner {
            display: flex; align-items: flex-start; gap: 14px;
            background: rgba(255, 159, 10, 0.1);
            border: 1px solid rgba(255, 159, 10, 0.3);
            border-radius: 16px; padding: 16px 20px; margin-bottom: 24px;
        }
        .warning-banner i { font-size: 22px; color: #ff9f0a; flex-shrink: 0; margin-top: 2px; }
        .warning-banner .wb-text { flex: 1; }
        .warning-banner .wb-title { font-weight: 700; font-size: 14px; color: #ff9f0a; margin-bottom: 4px; }
        .warning-banner .wb-body { font-size: 13px; color: var(--text-secondary); line-height: 1.6; }
        .warning-banner .wb-close {
            background: none; border: none; cursor: pointer;
            color: var(--text-secondary); font-size: 18px; padding: 0;
            flex-shrink: 0; line-height: 1;
        }
        .warning-banner .wb-close:hover { color: var(--text-primary); }

        /* ── Upload card ── */
        .upload-card {
            background: var(--theme-surface); backdrop-filter: blur(24px);
            border: 1px solid var(--theme-border); border-radius: 22px;
            padding: 24px 28px; margin-bottom: 28px;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;
        }
        .upload-card h3 { color: var(--theme-gold); font-size: 18px; margin-bottom: 4px; }
        .upload-card p { color: var(--text-secondary); font-size: 13px; }
        .upload-card ul { color: var(--text-secondary); font-size: 12px; margin-top: 8px; list-style: disc; padding-right: 18px; }
        .upload-card-actions { display: flex; gap: 10px; flex-wrap: wrap; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; border-radius: 14px; font-weight: 700;
            font-size: 14px; text-decoration: none; cursor: pointer;
            border: none; transition: all 0.3s ease;
            font-family: 'Tajawal', sans-serif; white-space: nowrap;
        }
        .btn-primary { background: var(--theme-gold); color: #000; }
        .btn-primary:hover { background: var(--theme-gold-dark); transform: translateY(-2px); }
        .btn-outline {
            background: var(--theme-surface-2); color: var(--text-secondary);
            border: 1px solid var(--theme-border);
        }
        .btn-outline:hover { background: rgba(198,166,117,0.08); color: var(--text-primary); border-color: var(--theme-gold-soft); }
        .btn-sm { padding: 8px 16px; font-size: 12px; border-radius: 10px; }
        .btn-block { width: 100%; justify-content: center; }
        .btn-gold { background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark)); color: #000; }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(196,150,58,0.3); }

        .section-title { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 18px; }
        .section-title span { color: var(--text-secondary); font-size: 13px; font-weight: 400; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 36px; }

        .template-card {
            background: var(--theme-surface);
            backdrop-filter: blur(24px);
            border: 1px solid var(--theme-border);
            border-radius: 22px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .template-card:hover { transform: translateY(-8px); box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .template-card img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .template-card .body { padding: 18px; }
        .template-card .body h5 { color: var(--theme-gold); font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .template-card .body p { color: var(--text-secondary); font-size: 12px; margin-bottom: 12px; }
        .template-card .body .meta { font-size: 12px; color: var(--text-secondary); margin-bottom: 12px; line-height: 1.8; }
        .template-card .body .actions { display: flex; flex-direction: column; gap: 8px; }

        .flash { padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
        .flash-success { background: var(--theme-success-soft, rgba(52,199,89,0.12)); color: var(--theme-success); border: 1px solid var(--theme-success-border, rgba(52,199,89,0.2)); }

        /* ── Course-type info banner ── */
        .type-banner {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 18px; border-radius: 14px; margin-bottom: 20px;
            font-size: 13px; font-weight: 600;
        }
        .type-banner.actual  { background: rgba(198,166,117,0.12); border: 1px solid rgba(198,166,117,0.3); color: var(--theme-gold); }
        .type-banner.training { background: rgba(45,164,191,0.10); border: 1px solid rgba(45,164,191,0.3); color: #2dd4bf; }
        .type-banner i { font-size: 18px; flex-shrink: 0; }

        /* ── Email Modal ── */
        .email-modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 1000;
            background: rgba(0,0,0,0.65); backdrop-filter: blur(6px);
            align-items: center; justify-content: center; padding: 20px;
        }
        .email-modal-overlay.open { display: flex; }
        .email-modal {
            background: var(--theme-surface);
            border: 1px solid var(--theme-border);
            border-radius: 28px; width: 100%; max-width: 520px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.55);
            animation: emSlideUp 0.24s cubic-bezier(.22,.68,0,1.2);
            overflow: hidden;
        }
        @keyframes emSlideUp { from { opacity:0; transform:translateY(28px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }
        .email-modal-header {
            display: flex; align-items: center; gap: 14px;
            padding: 22px 26px 18px;
            border-bottom: 1px solid var(--theme-border);
        }
        .email-modal-header .em-icon {
            width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
            background: rgba(198,166,117,0.15); border: 1px solid rgba(198,166,117,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: var(--theme-gold);
        }
        .email-modal-header h2 { font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 2px; }
        .email-modal-header p  { font-size: 12px; color: var(--text-secondary); }
        .email-modal-close {
            margin-right: auto; background: none; border: none; cursor: pointer;
            color: var(--text-secondary); font-size: 20px; padding: 4px; border-radius: 8px;
            line-height: 1; transition: 0.18s;
        }
        .email-modal-close:hover { color: var(--text-primary); background: var(--theme-surface-2); }
        .email-modal-body { padding: 22px 26px; }
        /* Preview strip */
        .em-preview {
            display: flex; gap: 14px; align-items: center;
            background: var(--theme-surface-2); border: 1px solid var(--theme-border);
            border-radius: 16px; padding: 14px; margin-bottom: 20px;
        }
        .em-preview img {
            width: 70px; height: 50px; object-fit: cover;
            border-radius: 10px; flex-shrink: 0;
        }
        .em-preview .em-info { flex: 1; }
        .em-preview .em-template-name { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
        /* Recipient row */
        .em-recipient {
            display: flex; gap: 12px;
            background: rgba(198,166,117,0.06); border: 1px solid rgba(198,166,117,0.2);
            border-radius: 14px; padding: 12px 16px; margin-bottom: 20px;
            align-items: center;
        }
        .em-recipient .em-avatar {
            width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
            background: var(--theme-gold-soft); display: flex; align-items: center;
            justify-content: center; font-size: 18px; color: var(--theme-gold);
        }
        .em-recipient .em-rname { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px; }
        .em-recipient .em-remail { font-size: 12px; color: var(--text-secondary); direction: ltr; text-align: right; }
        /* Message field */
        .em-label { font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px; display: block; }
        .em-textarea {
            width: 100%; padding: 12px 14px; border-radius: 14px;
            background: var(--theme-surface-2); border: 1px solid var(--theme-border);
            color: var(--text-primary); font-family: 'Tajawal', sans-serif; font-size: 13px;
            resize: none; min-height: 84px; line-height: 1.6;
            transition: border-color 0.2s;
        }
        .em-textarea:focus { outline: none; border-color: var(--theme-gold); }
        .em-textarea::placeholder { color: var(--text-secondary); opacity: .6; }
        /* Footer */
        .email-modal-footer {
            display: flex; gap: 10px; justify-content: flex-end;
            padding: 16px 26px 22px; border-top: 1px solid var(--theme-border);
        }
        .btn-send {
            background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark));
            color: #000; border: none; border-radius: 14px;
            padding: 11px 28px; font-weight: 700; font-size: 14px;
            cursor: pointer; font-family: 'Tajawal', sans-serif;
            display: inline-flex; align-items: center; gap: 8px;
            transition: 0.25s;
        }
        .btn-send:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(196,150,58,0.35); }
        .btn-send:disabled { opacity: .6; cursor: not-allowed; transform: none; }
        .btn-cancel-em {
            background: var(--theme-surface-2); color: var(--text-secondary);
            border: 1px solid var(--theme-border); border-radius: 14px;
            padding: 11px 22px; font-weight: 600; font-size: 14px;
            cursor: pointer; font-family: 'Tajawal', sans-serif; transition: 0.2s;
        }
        .btn-cancel-em:hover { color: var(--text-primary); background: var(--theme-surface); }

        /* ── Recommended badge overlaid on preset card image ── */
        .template-card { position: relative; }
        .recommended-badge {
            position: absolute; top: 10px; right: 10px; z-index: 2;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.2px;
            border: 1px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        .recommended-badge.actual-rec  { background: rgba(198,166,117,0.92); color: #1a1200; }
        .recommended-badge.training-rec { background: rgba(45,212,191,0.88); color: #003330; }
    </style>
</head>
<body>
    <div class="container">

        {{-- زر العودة أعلى يمين الصفحة --}}
        <div class="top-bar">
            <a href="{{ route('teacher.certificates.students') }}" class="back-btn">
                <i class="ri-arrow-right-line"></i> العودة لقائمة المستفيدين
            </a>
        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif

        {{-- تحذير إتمام المسار (يظهر فقط إذا تحقق عدم الإتمام) --}}
        @if($courseCompleted === false)
        <div class="warning-banner" id="completionWarning">
            <i class="ri-alert-line"></i>
            <div class="wb-text">
                <div class="wb-title">تنبيه: لم يُتمّ الطالب المسار بعد</div>
                <div class="wb-body">
                    <strong>{{ $student->name }}</strong> لم يُكمل جميع دروس مسار
                    <strong>{{ $student->course }}</strong> حتى الآن.
                    يمكنك إصدار الشهادة على أي حال، لكن يُنصح بالانتظار حتى يُتمّ الطالب المسار.
                </div>
            </div>
            <button class="wb-close" onclick="document.getElementById('completionWarning').style.display='none'" title="إغلاق">
                <i class="ri-close-line"></i>
            </button>
        </div>
        @endif

        @if($courseType)
        <div class="type-banner {{ $courseType }}">
            @if($courseType === 'actual')
                <i class="ri-graduation-cap-line"></i>
                <span>هذا مسار <strong>فعلي</strong> — القوالب الموصى بها موسومة بـ <strong>موصى به</strong> وتستخدم عنوان "شهادة إتمام"</span>
            @else
                <i class="ri-user-received-line"></i>
                <span>هذا مسار <strong>تدريبي</strong> — القوالب الموصى بها موسومة بـ <strong>موصى به</strong> وتستخدم عنوان "شهادة مشاركة"</span>
            @endif
        </div>
        @endif

        <div class="page-title">
            <h1>اختر قالب الشهادة</h1>
            <p>إصدار شهادة للمستفيد: <span class="badge">{{ $student->name }}</span></p>
            @if($selectedCourse)
                <p style="margin-top:6px;font-size:13px;color:var(--text-secondary);">
                    <i class="ri-book-2-line" style="color:var(--theme-gold);"></i>
                    المسار المحدد: <strong style="color:var(--theme-gold);">{{ $selectedCourse->name }}</strong>
                </p>
            @endif
        </div>

        @if($selectedCourse)
        <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-radius:16px;
                    background:rgba(198,166,117,0.1);border:1px solid rgba(198,166,117,0.3);margin-bottom:20px;">
            <i class="ri-book-2-fill" style="font-size:22px;color:var(--theme-gold);flex-shrink:0;"></i>
            <div style="flex:1;">
                <div style="font-weight:700;font-size:14px;color:var(--theme-gold);">إصدار شهادة لمسار: {{ $selectedCourse->name }}</div>
                <div style="font-size:12px;color:var(--text-secondary);margin-top:2px;">
                    اختر قالباً أدناه وسيُسجَّل تلقائياً تحت هذا المسار عند الإصدار.
                </div>
            </div>
            <a href="{{ route('teacher.certificates.gallery', $student) }}" style="font-size:12px;color:var(--text-muted);text-decoration:none;white-space:nowrap;">
                <i class="ri-close-line"></i> إلغاء تحديد المسار
            </a>
        </div>
        @endif

        <!-- Upload Card -->
        <div class="upload-card">
            <div>
                <h3><i class="ri-cloud-line"></i> قوالبك الخاصة</h3>
                <p>ارفع قالباً جاهزاً أو أنشئ قالبك من الصفر.</p>
                <ul>
                    <li>يفضل أن يكون القالب واضحاً مع خلفية مناسبة للشهادة.</li>
                    <li>النصوص الأساسية قابلة للتعديل لاحقاً من محرر القالب.</li>
                </ul>
            </div>
            <div class="upload-card-actions">
                <a href="{{ route('teacher.certificates.custom.upload.view', $student) }}{{ $selectedCourse ? '?course_id='.$selectedCourse->id : '' }}" class="btn btn-primary">
                    <i class="ri-upload-line"></i> رفع قالب
                </a>
                @php
                    $createUrl = route('teacher.certificates.custom.create', $student);
                    $createParams = [];
                    if ($selectedCourse) $createParams['course_id'] = $selectedCourse->id;
                    if ($courseType) $createParams['course_type'] = $courseType;
                    if ($createParams) $createUrl .= '?' . http_build_query($createParams);
                @endphp
                <a href="{{ $createUrl }}" class="btn btn-gold">
                    <i class="ri-pencil-ruler-2-line"></i> إنشاء قالب مخصص
                </a>
            </div>
        </div>

        <!-- Uploaded Templates -->
        @if($uploadedTemplates->isNotEmpty())
            <div class="section-title">
                قوالبي المرفوعة
                <span>{{ $uploadedTemplates->count() }} قالب</span>
            </div>
            <div class="grid">
                @foreach($uploadedTemplates as $t)
                    <div class="template-card">
                        <img src="{{ $t->background_type === 'image' && $t->background_image ? asset('storage/'.$t->background_image) : asset('image/logono.png') }}" alt="{{ $t->name }}">
                        <div class="body">
                            <h5>{{ $t->name }}</h5>
                            <p>قالب خاص بك</p>
                            <div class="meta">
                                <div>العنوان: {{ $t->title }}</div>
                                <div>المتلقي: {{ $t->recipient_name ?? $student->name }}</div>
                            </div>
                            <div class="actions">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                                    <a href="{{ route('teacher.certificates.custom.show', [$student, $t]) }}" class="btn btn-outline btn-sm">معاينة</a>
                                    <a href="{{ route('teacher.certificates.custom.download', [$student, $t]) }}" class="btn btn-primary btn-sm">تحميل PDF</a>
                                    <a href="{{ route('teacher.certificates.custom.edit', [$student, $t]) }}" class="btn btn-outline btn-sm">تعديل</a>
                                    <button type="button" class="btn btn-outline btn-sm email-trigger" style="width:100%;"
                                        data-action="{{ route('teacher.certificates.custom.email', [$student, $t]) }}"
                                        data-name="{{ $t->name }}"
                                        data-thumbnail="{{ $t->background_type === 'image' && $t->background_image ? asset('storage/'.$t->background_image) : asset('image/logono.png') }}">
                                        <i class="ri-mail-send-line"></i> إرسال
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('teacher.certificates.custom.destroy', [$student, $t]) }}" style="display:block;width:100%;" class="gallery-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm btn-block" style="color:var(--theme-danger);border-color:rgba(255,59,48,0.2);">
                                        <i class="ri-delete-bin-line"></i> حذف القالب
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Template Library (from other students) -->
        @if(isset($libraryTemplates) && $libraryTemplates->isNotEmpty())
            <div class="section-title" style="margin-top:16px;">
                مكتبة قوالبي
                <span>{{ $libraryTemplates->count() }} قالب — أُنشئت لمستفيدين آخرين، انسخها لهذا المستفيد</span>
            </div>
            <div class="grid">
                @foreach($libraryTemplates as $t)
                    <div class="template-card">
                        <img src="{{ $t->background_type === 'image' && $t->background_image ? asset('storage/'.$t->background_image) : asset('image/logono.png') }}" alt="{{ $t->name }}">
                        <div class="body">
                            <h5>{{ $t->name }}</h5>
                            <p style="color:var(--text-muted);font-size:11px;">
                                <i class="ri-user-3-line"></i>
                                أُنشئ لـ: {{ $t->certificateStudent?->name ?? $t->recipient_name ?? '—' }}
                            </p>
                            <div class="meta">
                                <div>العنوان: {{ $t->title }}</div>
                            </div>
                            <div class="actions">
                                <form method="POST" action="{{ route('teacher.certificates.custom.copy', [$student, $t]) }}{{ $selectedCourse ? '?course_id='.$selectedCourse->id : '' }}" style="display:block;width:100%;">
                                    @csrf
                                    <button type="submit" class="btn btn-gold btn-sm btn-block" style="width:100%;justify-content:center;">
                                        <i class="ri-file-copy-line"></i> نسخ واستخدام لهذا المستفيد
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Preset Templates -->
        @php
            // 4th element: 'actual' | 'training' | 'both'
            $presets = [
                1 => ['qw1.jpeg', 'القالب الكلاسيكي', 'يتميز بزخارف رسمية وخلفية تقليدية.', 'actual'],
                2 => ['qw2.jpeg', 'القالب العصري', 'تصميم بسيط وألوان هادئة للشركات الناشئة.', 'training'],
                3 => ['qw3.jpeg', 'القالب الذهبي', 'تصميم فاخر مخصص للمناسبات الخاصة والجوائز.', 'both'],
                4 => ['qw4.jpeg', 'الدرع الرقمي', 'تصميم مستقبلي بنمط تقني عالي.', 'actual'],
                5 => ['qw5.jpeg', 'القالب الأكاديمي', 'تصميم كلاسيكي بوقار مؤسسي.', 'actual'],
                6 => ['qw6.jpeg', 'الإبداع الهندسي', 'زخارف هندسية متداخلة تعبر عن الدقة.', 'actual'],
                7 => ['qw7.jpeg', 'الوسام المهني', 'تصميم رصين يركز على الكفاءة والخبرة.', 'training'],
                8 => ['qw8.jpeg', 'الطراز الأكاديمي', 'خلفية بنقوش خفيفة توحي بالعراقة العلمية.', 'actual'],
                9 => ['qw9.jpeg', 'مودرن جرافيك', 'تصميم معاصر يجمع بين الأناقة والابتكار.', 'both'],
            ];
        @endphp
        <div class="section-title">القوالب الجاهزة</div>
        <div class="grid">
            @foreach($presets as $num => $p)
                @php $isRecommended = $courseType && ($p[3] === 'both' || $p[3] === $courseType); @endphp
                <div class="template-card">
                    @if($isRecommended)
                        <div class="recommended-badge {{ $courseType }}-rec">موصى به</div>
                    @endif
                    <img src="{{ asset('image/'.$p[0]) }}" alt="{{ $p[1] }}">
                    <div class="body">
                        <h5>{{ $p[1] }}</h5>
                        <p>{{ $p[2] }}</p>
                        <div class="actions">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                                <a href="{{ route('teacher.certificates.preview', [$num, $student]) }}" class="btn btn-outline btn-sm">معاينة</a>
                                <a href="{{ route('teacher.certificates.download', [$num, $student]) }}" class="btn btn-primary btn-sm">تحميل PDF</a>
                                <a href="{{ route('teacher.certificates.custom.create', ['student' => $student, 'preset' => $num]) }}" class="btn btn-outline btn-sm">تعديل</a>
                                <button type="button" class="btn btn-outline btn-sm email-trigger" style="width:100%;"
                                    data-action="{{ route('teacher.certificates.email', [$student, $num]) }}"
                                    data-name="{{ $p[1] }}"
                                    data-thumbnail="{{ asset('image/'.$p[0]) }}">
                                    <i class="ri-mail-send-line"></i> إرسال
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- Email Confirmation Modal --}}
    <div class="email-modal-overlay" id="emailModal">
        <div class="email-modal">
            <div class="email-modal-header">
                <div class="em-icon"><i class="ri-mail-send-line"></i></div>
                <div>
                    <h2>إرسال الشهادة بالبريد</h2>
                    <p>مراجعة بيانات الإرسال قبل التأكيد</p>
                </div>
                <button class="email-modal-close" id="emailModalClose" title="إغلاق"><i class="ri-close-line"></i></button>
            </div>
            <div class="email-modal-body">
                {{-- Template preview --}}
                <div class="em-preview">
                    <img id="emThumbnail" src="" alt="قالب الشهادة">
                    <div class="em-info">
                        <div class="em-template-name" id="emTemplateName"></div>
                        <div style="font-size:12px;color:var(--text-secondary);">القالب المختار</div>
                    </div>
                    <i class="ri-award-fill" style="font-size:22px;color:var(--theme-gold);flex-shrink:0;"></i>
                </div>
                {{-- Recipient --}}
                <div class="em-recipient">
                    <div class="em-avatar"><i class="ri-user-3-line"></i></div>
                    <div>
                        <div class="em-rname">{{ $student->name }}</div>
                        <div class="em-remail">{{ $student->email ?: 'لم يُحدد بريد إلكتروني' }}</div>
                    </div>
                </div>
                {{-- Personal message --}}
                <label class="em-label" for="emMessage"><i class="ri-chat-1-line"></i> رسالة شخصية (اختيارية)</label>
                <textarea id="emMessage" class="em-textarea" placeholder="أضف رسالة تشجيعية أو تهنئة شخصية للطالب..." maxlength="500"></textarea>
            </div>
            <div class="email-modal-footer">
                <button class="btn-cancel-em" id="emailModalCancel">إلغاء</button>
                <form id="emailForm" method="POST" action="" style="display:contents;">
                    @csrf
                    <input type="hidden" name="message" id="emMessageHidden">
                    <button type="submit" class="btn-send" id="emailSendBtn"
                        @if(!$student->email) disabled title="لا يوجد بريد إلكتروني لهذا الطالب" @endif>
                        <i class="ri-send-plane-line"></i> إرسال الشهادة
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Delete confirmation
            document.querySelectorAll('.gallery-delete-form').forEach(function (f) {
                f.addEventListener('submit', function (e) {
                    if (!confirm('هل أنت متأكد من حذف هذا القالب؟')) {
                        e.preventDefault();
                    }
                });
            });

            // Email modal
            const overlay   = document.getElementById('emailModal');
            const form      = document.getElementById('emailForm');
            const thumbnail = document.getElementById('emThumbnail');
            const nameEl    = document.getElementById('emTemplateName');
            const msgField  = document.getElementById('emMessage');
            const msgHidden = document.getElementById('emMessageHidden');

            function openEmail(btn) {
                form.action      = btn.dataset.action;
                thumbnail.src    = btn.dataset.thumbnail;
                nameEl.textContent = btn.dataset.name;
                msgField.value   = '';
                overlay.classList.add('open');
                setTimeout(() => msgField.focus(), 280);
            }
            function closeEmail() {
                overlay.classList.remove('open');
            }

            document.querySelectorAll('.email-trigger').forEach(btn =>
                btn.addEventListener('click', () => openEmail(btn))
            );
            document.getElementById('emailModalClose').addEventListener('click', closeEmail);
            document.getElementById('emailModalCancel').addEventListener('click', closeEmail);
            overlay.addEventListener('click', e => { if (e.target === overlay) closeEmail(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEmail(); });

            // Sync textarea → hidden input before submit
            form.addEventListener('submit', function () {
                msgHidden.value = msgField.value.trim();
            });
        });
    </script>
    @include('components.account-theme-foot')
</body>
</html>
