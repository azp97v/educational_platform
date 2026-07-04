<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $editingTemplate ? 'تعديل القالب' : 'إنشاء قالب شهادة مخصص' }}</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Cairo:wght@400;600;700;800&family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('image/logono.png') }}" type="image/x-icon">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: radial-gradient(circle at top left, rgba(198,166,117,0.18), transparent 22%),
                        linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-surface-2) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 24px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .hero-card, .form-card, .preview-card {
            background: var(--theme-surface);
            backdrop-filter: blur(24px);
            border: 1px solid var(--theme-border);
            border-radius: 22px;
        }
        .hero-card { padding: 24px 28px; margin-bottom: 20px; }
        .form-card, .preview-card { padding: 18px 20px; }
        .chip {
            display: inline-block; padding: 6px 14px; border-radius: 999px;
            background: var(--theme-gold-soft); color: var(--theme-gold);
            font-size: 13px; font-weight: 700;
        }
        .preview-surface {
            border-radius: 20px; padding: 16px;
            background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark));
            color: #000;
        }
        .preview-surface img { filter: brightness(0) invert(0); }
        .designer-preview {
            position: relative; border-radius: 28px; overflow: hidden;
            width: 100%; aspect-ratio: 1.41 / 1; min-height: 560px;
            max-width: 980px; margin: 0 auto; padding: 40px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.22);
            isolation: isolate;
        }
        .designer-preview .background-layer { position:absolute; inset:0; background-color: transparent; background-size: cover; background-position: center; background-repeat: no-repeat; z-index:0; pointer-events:none; }
        .designer-preview .overlay { position:absolute; inset:0; background: rgba(0,0,0,0.08); pointer-events:none; z-index:2; }
        .designer-preview .stamp { position:absolute; top:30px; left:30px; border-radius:50%; border:3px solid rgba(255,255,255,0.75); display:grid; place-items:center; font-weight:800; font-size:0.95rem; background: rgba(255,255,255,0.15); backdrop-filter:blur(6px); z-index:3; }
        .designer-preview .logo-box { border-radius:24px; background:rgba(255,255,255,0.16); display:grid; place-items:center; padding:10px; }
        .designer-preview .logo-box img { max-width:90px; max-height:90px; object-fit:contain; }
        .designer-preview .text-block { position:absolute; width: 78%; text-align:center; color:#fff; text-shadow: 0 2px 12px rgba(0,0,0,0.2); white-space: pre-wrap; word-break: break-word; left: 50%; right: auto; transform: translateX(-50%); z-index:3; }
        .designer-preview .text-block:focus { outline: 2px dashed rgba(255,255,255,0.7); outline-offset: 4px; border-radius: 10px; }
        .designer-preview .draggable-element { cursor: grab; user-select:none; z-index:3; }
        .designer-preview .draggable-element.dragging { cursor: grabbing; }
        .designer-preview .draggable-element.selected { outline: 2px dashed rgba(255,255,255,0.75); outline-offset: 4px; border-radius: 12px; }
        .designer-preview .helper-lines { position:absolute; inset:0; pointer-events:none; z-index:4; }
        .designer-preview .helper-line { position:absolute; background: rgba(255,255,255,0.85); box-shadow: 0 0 0 1px rgba(196,150,58,0.25); }
        .designer-preview .helper-line.vertical { width:2px; top:0; bottom:0; }
        .designer-preview .helper-line.horizontal { height:2px; left:0; right:0; }
        .designer-preview .helper-line.diagonal { height:2px; transform-origin:left center; }
        .designer-preview .preview-title { font-weight:800; }
        .designer-preview .preview-subtitle { font-weight:600; opacity:0.95; }
        .designer-preview .preview-name { font-weight:700; border-bottom: 2px solid rgba(255,255,255,0.55); padding-bottom:8px; display:inline-block; }
        .designer-preview .preview-body { line-height:1.8; }
        .designer-preview .footer-line { position:absolute; bottom:30px; right:34px; left:34px; display:flex; justify-content:space-between; color:#fff; border-top:1px solid rgba(255,255,255,0.3); padding-top:16px; font-size:0.95rem; }
        .small-label { font-size: 0.82rem; color: var(--text-secondary); }
        .xy-label { font-size: 0.72rem; color: var(--theme-gold); font-weight: 700; display: block; margin-top: 6px; }
        .xy-label:first-of-type { margin-top: 2px; }
        .flex-row { display: flex; flex-wrap: wrap; gap: 12px; }
        .flex-row > * { flex: 1 1 200px; }

        .control-group {
            background: var(--theme-surface-2);
            border-radius: 16px; padding: 14px 16px;
            border: 1px solid var(--theme-border-light);
        }
        .control-group .section-title { font-size: 0.88rem; font-weight:700; color: var(--theme-gold); margin-bottom: 10px; }
        .control-group .flex-row > div { display:flex; flex-direction:column; gap:6px; }

        label { font-size: 13px; color: var(--text-secondary); font-weight: 600; }
        input[type="text"],
        input[type="color"],
        textarea,
        select {
            font-family: 'Tajawal', sans-serif;
            background: var(--theme-input-bg, rgba(255,255,255,0.06));
            border: 1px solid var(--theme-border);
            border-radius: 12px;
            padding: 10px 14px;
            color: var(--text-primary);
            font-size: 14px;
            width: 100%;
            transition: 0.2s;
        }
        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--theme-gold);
            box-shadow: 0 0 0 3px var(--theme-gold-soft);
            outline: none;
        }
        input[type="color"] { height: 48px; padding: 4px; cursor: pointer; }
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 2px; }
        input[type="color"]::-webkit-color-swatch { border-radius: 8px; border: none; }
        textarea { resize: vertical; min-height: 60px; }
        select { cursor: pointer; }
        input[type="range"] {
            -webkit-appearance: none; width: 100%; height: 6px;
            background: var(--theme-gold-soft); border-radius: 3px;
            outline: none;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none; width: 18px; height: 18px;
            border-radius: 50%; background: var(--theme-gold); cursor: pointer;
            border: 2px solid var(--theme-border);
        }
        input[type="file"] {
            font-family: 'Tajawal', sans-serif;
            background: var(--theme-input-bg, rgba(255,255,255,0.06));
            border: 1px dashed var(--theme-border);
            border-radius: 12px;
            padding: 10px 14px;
            color: var(--text-secondary);
            font-size: 13px;
            width: 100%;
        }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 12px; font-weight: 700;
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
        .btn-outline:hover { background: var(--theme-gold-soft); color: var(--text-primary); border-color: var(--theme-border-strong); }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; }
        .btn-sm { padding: 6px 14px; font-size: 12px; border-radius: 10px; }

        .alert { padding: 14px 20px; border-radius: 14px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
        .alert-success { background: var(--theme-success-soft, rgba(52,199,89,0.12)); color: var(--theme-success); border: 1px solid var(--theme-success-border, rgba(52,199,89,0.2)); }
        .alert-danger { background: rgba(255,59,48,0.12); color: var(--theme-danger); border: 1px solid rgba(255,59,48,0.2); }

        .designer-layout { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); gap: 16px; align-items: stretch; }
        .designer-layout .preview-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
        .designer-layout .form-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
        .sticky-preview { position: sticky; top: 1rem; }

        .mini-preview-shell {
            position: fixed; left: 1rem; bottom: 1rem;
            width: min(380px, calc(100vw - 2rem));
            max-width: 380px; z-index: 1050;
            opacity: 0; pointer-events: none;
            transform: translateY(16px) scale(0.96);
            transition: all 0.28s ease;
        }
        .mini-preview-shell.is-visible { opacity: 1; pointer-events: auto; transform: translateY(0) scale(1); }
        .mini-preview-card {
            border-radius: 20px; overflow: hidden;
            background: var(--theme-surface);
            border: 1px solid var(--theme-gold-soft);
            box-shadow: 0 20px 45px rgba(0,0,0,0.4);
        }
        .mini-preview-header {
            display:flex; justify-content:space-between; align-items:center;
            padding:10px 14px;
            background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark));
            color: #000; font-size:14px; font-weight:700;
            cursor: grab; user-select:none;
        }
        .mini-preview-header:active { cursor: grabbing; }
        .mini-preview-body { padding: 12px; }
        .mini-preview-surface {
            position: relative; border-radius: 16px; overflow: hidden;
            aspect-ratio: 1.41 / 1; min-height: 240px;
            background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark));
            color: white; padding: 18px; isolation:isolate;
        }
        .mini-preview-surface .background-layer { position:absolute; inset:0; background-color: transparent; background-size: cover; background-position: center; background-repeat: no-repeat; z-index:0; pointer-events:none; }
        .mini-preview-surface .overlay { position:absolute; inset:0; background: rgba(0,0,0,0.08); pointer-events:none; z-index:2; }
        .mini-preview-surface .mini-stamp { position:absolute; top:16px; left:16px; border-radius:50%; border:2px solid rgba(255,255,255,0.75); display:grid; place-items:center; font-weight:800; font-size:0.74rem; background: rgba(255,255,255,0.16); }
        .mini-preview-surface .mini-logo-box { border-radius:16px; background:rgba(255,255,255,0.16); display:grid; place-items:center; padding:8px; }
        .mini-preview-surface .mini-logo-box img { max-width:44px; max-height:44px; object-fit:contain; }
        .mini-preview-surface .mini-text-block { position:absolute; width:78%; text-align:center; text-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        .mini-preview-surface .mini-title { font-weight:800; }
        .mini-preview-surface .mini-subtitle { font-weight:600; opacity:0.95; }
        .mini-preview-surface .mini-name { font-weight:700; border-bottom:1px solid rgba(255,255,255,0.55); padding-bottom:4px; display:inline-block; }
        .mini-preview-surface .mini-body { line-height:1.4; font-size:12px; }
        .mini-preview-resize-handle { position:absolute; left:6px; bottom:6px; width:16px; height:16px; border-left:2px solid var(--theme-gold); border-bottom:2px solid var(--theme-gold); cursor:nwse-resize; }

        .template-list { display: flex; flex-direction: column; gap: 8px; }
        .template-list-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 14px;
            background: var(--theme-surface-2);
            border-radius: 12px;
            border: 1px solid var(--theme-border-light);
        }
        .template-list-item span { color: var(--text-primary); font-size: 14px; font-weight: 600; }
        .template-list-item .actions { display: flex; gap: 8px; }
        .preset-grid { display: flex; flex-direction: column; gap: 8px; }
        .preset-grid a {
            display: block; text-align: center;
            padding: 12px; border-radius: 12px;
            background: var(--theme-surface-2);
            border: 1px solid var(--theme-border);
            color: var(--theme-gold); text-decoration: none;
            font-weight: 600; font-size: 14px; transition: 0.2s;
        }
        .preset-grid a:hover { background: var(--theme-gold-soft); }

        .d-flex { display: flex; }
        .flex-wrap { flex-wrap: wrap; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        .mb-0 { margin-bottom: 0; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mt-4 { margin-top: 16px; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .text-muted { color: var(--text-secondary); }
        .text-center { text-align: center; }
        .fs-5 { font-size: 1.15rem; }
        .small { font-size: 0.85rem; }
        .w-full { width: 100%; }

        @media (max-width: 991.98px) {
            .designer-layout { grid-template-columns: 1fr; }
            .sticky-preview { position: static; }
            .mini-preview-shell { left: 0.75rem; bottom: 0.75rem; width: min(340px, calc(100vw - 1.5rem)); }
            .flex-row > * { flex: 1 1 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="hero-card">
        <div class="flex-row align-items-center">
            <div style="flex:2;min-width:260px;">
                <span class="chip">معاينة مباشرة قبل التصدير</span>
                <h1 class="fw-bold mt-3 mb-2" style="font-size:24px;color:var(--theme-gold);">{{ $editingTemplate ? 'تعديل قالبك المخصص' : (!empty($presetData['template_label']) ? 'تعديل ' . $presetData['template_label'] : 'أنشئ قالب شهادة مخصص من الصفر') }}</h1>
                @if(!empty($presetData['template_label']) && !$editingTemplate && request()->has('preset'))
                    <div class="chip mt-2">القالب المختار: {{ $presetData['template_label'] }}</div>
                @endif
                <p class="text-muted fs-5 mb-0">غيّر الألوان، النصوص، المواضع، والخلفية ثم تابع التغييرات مباشرة على الصورة النهائية قبل الحفظ أو التصدير.</p>
            </div>
            <div style="flex:1;min-width:200px;text-align:center;">
                <div class="preview-surface">
                    <img src="{{ asset('image/logono.png') }}" alt="logo" style="max-width: 80px;">
                    <h5 class="mt-3 mb-2">محرر القوالب</h5>
                    <p class="mb-0 small">يدعم التعديل على القوالب الحالية أو من البداية.</p>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $initialValue = function ($field, $default = null) use ($editingTemplate, $presetData) {
            return old($field, $editingTemplate ? ($editingTemplate->{$field} ?? $default) : ($presetData[$field] ?? $default));
        };
        $previewBackgroundImage = $editingTemplate && $editingTemplate->background_type === 'image' && $editingTemplate->background_image ? asset('storage/' . $editingTemplate->background_image) : '';
        $previewLogoImage = $editingTemplate && $editingTemplate->logo_image ? asset('storage/' . $editingTemplate->logo_image) : asset('image/logono.png');
    @endphp

    <div class="designer-layout">
        <div class="preview-col">

        <!-- Main Preview -->
        <div class="preview-card sticky-preview mb-3" id="mainPreviewSection">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color:var(--theme-gold);">المعاينة المباشرة</h5>
                <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-outline btn-sm"><i class="ri-arrow-right-line"></i> العودة للمعرض</a>
            </div>
            <div class="designer-preview" id="livePreview" data-background-image="{{ $previewBackgroundImage }}" data-logo-image="{{ $previewLogoImage }}" data-editor-template-id="{{ $editingTemplate?->id ?? 0 }}" data-editor-mode="{{ $editingTemplate ? 'edit' : ($presetData ? 'preset' : 'create') }}" style="background: linear-gradient(135deg, {{ $initialValue('primary_color', '#C4963A') }} 0%, {{ $initialValue('secondary_color', '#A07A28') }} 50%, {{ $initialValue('accent_color', '#D4A84B') }} 100%); border-radius: {{ $initialValue('border_radius', 30) }}px;">
                <div class="background-layer" id="previewBackgroundLayer"></div>
                <div class="overlay" style="opacity: {{ ($initialValue('overlay_opacity', 15) / 100) }};"></div>
                @if($initialValue('show_stamp', true))
                    <div class="stamp" id="previewStamp" style="width: {{ $initialValue('stamp_size', 120) }}px; height: {{ $initialValue('stamp_size', 120) }}px;">شهادة</div>
                @endif
                @if($initialValue('show_logo', true))
                    <div class="logo-box draggable-element" id="previewLogo" data-role="logo" style="position:absolute;right: {{ $initialValue('logo_x', 0) + 30 }}px; top: {{ $initialValue('logo_y', 0) + 30 }}px; width: {{ $initialValue('logo_width', 110) }}px; height: {{ $initialValue('logo_width', 110) }}px;">
                        <img src="{{ $previewLogoImage }}" id="logoPreview" alt="logo">
                    </div>
                @endif
                <div class="text-block preview-title draggable-element" id="previewTitle" contenteditable="false" data-field="title" data-role="title" style="top: {{ $initialValue('title_y', 0) + 140 }}px; font-size: {{ $initialValue('title_size', 38) }}px; color: {{ $initialValue('title_color', '#ffffff') }}; font-family: '{{ $initialValue('font_family', 'Cairo') }}', sans-serif; text-align: {{ $initialValue('text_align', 'center') }};">{{ $initialValue('title', 'شهادة إتمام') }}</div>
                <div class="text-block preview-subtitle draggable-element" id="previewSubtitle" contenteditable="false" data-field="subtitle" data-role="subtitle" style="top: {{ $initialValue('subtitle_y', 0) + 210 }}px; font-size: {{ $initialValue('subtitle_size', 20) }}px; color: {{ $initialValue('subtitle_color', '#ffffff') }}; font-family: '{{ $initialValue('font_family', 'Cairo') }}', sans-serif; text-align: {{ $initialValue('text_align', 'center') }};">{{ $initialValue('subtitle', 'تقديراً لجهودكم') }}</div>
                <div class="text-block preview-name draggable-element" id="previewName" contenteditable="false" data-field="recipient_name" data-role="recipient_name" style="top: {{ $initialValue('name_y', 0) + 300 }}px; font-size: {{ $initialValue('name_size', 32) }}px; color: {{ $initialValue('name_color', '#ffffff') }}; font-family: '{{ $initialValue('font_family', 'Cairo') }}', sans-serif; text-align: {{ $initialValue('text_align', 'center') }};">{{ $initialValue('recipient_name', $student->name) }}</div>
                <div class="text-block preview-body draggable-element" id="previewBody" contenteditable="false" data-field="body_text" data-role="body_text" style="top: {{ $initialValue('body_y', 0) + 370 }}px; font-size: {{ $initialValue('body_size', 18) }}px; color: {{ $initialValue('body_color', '#ffffff') }}; font-family: '{{ $initialValue('font_family', 'Cairo') }}', sans-serif; text-align: {{ $initialValue('text_align', 'center') }};">{{ $initialValue('body_text', 'تمت الموافقة على استكمال البرنامج بنجاح وبتقدير عالٍ من منصة اجلال التعليمية.') }}</div>
                <div class="footer-line">
                    <span>البرنامج: {{ $student->course }}</span>
                    <span>الدرجة: {{ $student->degree }}</span>
                    <span>منصة اجلال التعليمية</span>
                </div>
            </div>
        </div>

        </div>
        <div class="form-col">

        <!-- Form Controls -->
        <div class="form-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0" style="color:var(--theme-gold);">إعدادات التخصيص</h4>
            </div>

            <form action="{{ $editingTemplate ? route('teacher.certificates.custom.update', [$student, $editingTemplate]) : route('teacher.certificates.custom.store', $student) }}" method="POST" enctype="multipart/form-data" id="templateForm" data-editor-template-id="{{ $editingTemplate?->id ?? 0 }}" data-editor-mode="{{ $editingTemplate ? 'edit' : ($presetData ? 'preset' : 'create') }}">
                @csrf
                @if($editingTemplate)
                    @method('PUT')
                @endif

                <div class="control-group mb-3">
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">اسم القالب</label>
                            <input type="text" name="name" class="w-full" value="{{ $initialValue('name', '') }}" required>
                        </div>
                        <div>
                            <label class="fw-semibold">عنوان الشهادة</label>
                            <input type="text" name="title" class="w-full" value="{{ $initialValue('title', 'شهادة إتمام') }}" required>
                        </div>
                        <div>
                            <label class="fw-semibold">اسم المستلم الظاهر في الشهادة</label>
                            <input type="text" name="recipient_name" class="w-full" value="{{ $initialValue('recipient_name', $student->name) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">العنوان الفرعي</label>
                            <input type="text" name="subtitle" class="w-full" value="{{ $initialValue('subtitle', 'تقديراً لجهودكم') }}">
                        </div>
                        <div style="flex:1 1 100%;">
                            <label class="fw-semibold">نص الجسم الرئيسي</label>
                            <textarea name="body_text" class="w-full" rows="2">{{ $initialValue('body_text', 'تمت الموافقة على استكمال البرنامج بنجاح وبتقدير عالٍ من منصة اجلال التعليمية.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="section-title">الألوان والتنسيق</div>
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">اللون الأساسي</label>
                            <input type="color" name="primary_color" value="{{ $initialValue('primary_color', '#C4963A') }}">
                        </div>
                        <div>
                            <label class="fw-semibold">اللون الثانوي</label>
                            <input type="color" name="secondary_color" value="{{ $initialValue('secondary_color', '#A07A28') }}">
                        </div>
                        <div>
                            <label class="fw-semibold">لون التمييز</label>
                            <input type="color" name="accent_color" value="{{ $initialValue('accent_color', '#D4A84B') }}">
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">لون العنوان</label>
                            <input type="color" name="title_color" value="{{ $initialValue('title_color', '#ffffff') }}">
                        </div>
                        <div>
                            <label class="fw-semibold">لون الفرعي</label>
                            <input type="color" name="subtitle_color" value="{{ $initialValue('subtitle_color', '#ffffff') }}">
                        </div>
                        <div>
                            <label class="fw-semibold">لون الاسم</label>
                            <input type="color" name="name_color" value="{{ $initialValue('name_color', '#ffffff') }}">
                        </div>
                        <div>
                            <label class="fw-semibold">لون الجسم</label>
                            <input type="color" name="body_color" value="{{ $initialValue('body_color', '#ffffff') }}">
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="section-title">الخلفية والهوية البصرية</div>
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">نوع الخلفية</label>
                            <select name="background_type">
                                <option value="gradient" {{ $initialValue('background_type', 'gradient') === 'gradient' ? 'selected' : '' }}>تدرج فاخر</option>
                                <option value="solid" {{ $initialValue('background_type', 'gradient') === 'solid' ? 'selected' : '' }}>لون صلب</option>
                                <option value="image" {{ $initialValue('background_type', 'gradient') === 'image' ? 'selected' : '' }}>صورة خلفية</option>
                            </select>
                        </div>
                        <div>
                            <label class="fw-semibold">الخط</label>
                            <select name="font_family">
                                <option value="Cairo" {{ $initialValue('font_family', 'Cairo') === 'Cairo' ? 'selected' : '' }}>Cairo</option>
                                <option value="Tajawal" {{ $initialValue('font_family', 'Cairo') === 'Tajawal' ? 'selected' : '' }}>Tajawal</option>
                                <option value="Montserrat" {{ $initialValue('font_family', 'Cairo') === 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                <option value="Playfair Display" {{ $initialValue('font_family', 'Cairo') === 'Playfair Display' ? 'selected' : '' }}>Playfair Display</option>
                                <option value="Roboto" {{ $initialValue('font_family', 'Cairo') === 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                <option value="Georgia, serif" {{ $initialValue('font_family', 'Cairo') === 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
                                <option value="Avenir Next, system-ui" {{ $initialValue('font_family', 'Cairo') === 'Avenir Next, system-ui' ? 'selected' : '' }}>Avenir Next</option>
                            </select>
                        </div>
                        <div>
                            <label class="fw-semibold">المحاذاة</label>
                            <select name="text_align">
                                <option value="center" {{ $initialValue('text_align', 'center') === 'center' ? 'selected' : '' }}>وسط</option>
                                <option value="right" {{ $initialValue('text_align', 'center') === 'right' ? 'selected' : '' }}>يمين</option>
                                <option value="left" {{ $initialValue('text_align', 'center') === 'left' ? 'selected' : '' }}>يسار</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="section-title">تحكم الخلفية</div>
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">حجم الخلفية</label>
                            <input type="range" name="background_size" min="70" max="220" value="{{ $initialValue('background_size', 100) }}">
                            <div class="small-label">{{ $initialValue('background_size', 100) }}%</div>
                        </div>
                        <div>
                            <label class="fw-semibold">موضع الخلفية أفقيًا</label>
                            <input type="range" name="background_position_x" min="0" max="100" value="{{ $initialValue('background_position_x', 50) }}">
                            <div class="small-label">{{ $initialValue('background_position_x', 50) }}%</div>
                        </div>
                        <div>
                            <label class="fw-semibold">موضع الخلفية عموديًا</label>
                            <input type="range" name="background_position_y" min="0" max="100" value="{{ $initialValue('background_position_y', 50) }}">
                            <div class="small-label">{{ $initialValue('background_position_y', 50) }}%</div>
                        </div>
                    </div>
                    <div class="small-label mt-2">يمكنك سحب الخلفية داخل المعاينة مباشرة لتغيير مكانها.</div>
                </div>

                <div class="control-group mb-3">
                    <div class="section-title">الوسوم والملفات</div>
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">شعار القالب</label>
                            <input type="file" name="logo_image" accept="image/*">
                        </div>
                        <div>
                            <label class="fw-semibold">صورة خلفية اختيارية</label>
                            <input type="file" name="background_image" accept="image/*">
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" id="removeBackgroundButton" class="btn btn-danger btn-sm">إزالة الخلفية</button>
                            </div>
                            <input type="hidden" name="remove_background" id="removeBackgroundField" value="0">
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">إخفاء الشعار</label>
                            <select name="show_logo">
                                <option value="1" {{ $initialValue('show_logo', true) ? 'selected' : '' }}>عرض</option>
                                <option value="0" {{ $initialValue('show_logo', true) ? '' : 'selected' }}>إخفاء</option>
                            </select>
                        </div>
                        <div>
                            <label class="fw-semibold">إخفاء الختم</label>
                            <select name="show_stamp">
                                <option value="1" {{ $initialValue('show_stamp', true) ? 'selected' : '' }}>عرض</option>
                                <option value="0" {{ $initialValue('show_stamp', true) ? '' : 'selected' }}>إخفاء</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="section-title">الأحجام والطبقات</div>
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">حجم العنوان</label>
                            <input type="range" name="title_size" min="20" max="60" value="{{ $initialValue('title_size', 38) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">حجم الترتيب الفرعي</label>
                            <input type="range" name="subtitle_size" min="14" max="28" value="{{ $initialValue('subtitle_size', 20) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">حجم اسم الطالب</label>
                            <input type="range" name="name_size" min="20" max="46" value="{{ $initialValue('name_size', 32) }}">
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">شفافية الطبقة</label>
                            <input type="range" name="overlay_opacity" min="0" max="100" value="{{ $initialValue('overlay_opacity', 15) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">تدوير الحواف</label>
                            <input type="range" name="border_radius" min="0" max="60" value="{{ $initialValue('border_radius', 30) }}">
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="section-title">مساعد التوجيه</div>
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">اختيار العنصر</label>
                            <select id="guideTarget">
                                <option value="title">العنوان</option>
                                <option value="subtitle">العنوان الفرعي</option>
                                <option value="recipient_name">اسم الطالب</option>
                                <option value="body_text">النص الأساسي</option>
                                <option value="logo">الشعار</option>
                            </select>
                        </div>
                        <div>
                            <label class="fw-semibold">تدوير</label>
                            <input type="range" id="rotationRange" min="-180" max="180" value="0">
                            <div class="small-label" id="rotationHint">0°</div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-outline btn-sm" data-guide-action="center-h"><i class="ri-align-center"></i> توسيط أفقي</button>
                        <button type="button" class="btn btn-outline btn-sm" data-guide-action="center-v"><i class="ri-align-vertically"></i> توسيط عمودي</button>
                        <button type="button" class="btn btn-outline btn-sm" data-guide-action="snap-horizontal">سطر أفقي</button>
                        <button type="button" class="btn btn-outline btn-sm" data-guide-action="snap-vertical">سطر عمودي</button>
                        <button type="button" class="btn btn-outline btn-sm" data-guide-action="rotate-45">45°</button>
                        <button type="button" class="btn btn-outline btn-sm" data-guide-action="rotate-90">90°</button>
                    </div>
                    <div class="small-label mt-2">اسحب أي عنصر داخل المعاينة وسيظهر سطر مساعد عند التقارب مع الوسط أو السطر الأفقي/العمودي أو عند الوصول إلى زاوية 45°.</div>
                </div>

                <div class="control-group mb-3">
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">تحريك العنوان</label>
                            <span class="xy-label">أفقي (X)</span>
                            <input type="range" name="title_x" min="-220" max="220" value="{{ $initialValue('title_x', 0) }}">
                            <span class="xy-label">عمودي (Y)</span>
                            <input type="range" name="title_y" min="-120" max="220" value="{{ $initialValue('title_y', 0) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">تحريك العنوان الفرعي</label>
                            <span class="xy-label">أفقي (X)</span>
                            <input type="range" name="subtitle_x" min="-220" max="220" value="{{ $initialValue('subtitle_x', 0) }}">
                            <span class="xy-label">عمودي (Y)</span>
                            <input type="range" name="subtitle_y" min="-120" max="220" value="{{ $initialValue('subtitle_y', 0) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">تحريك اسم الطالب</label>
                            <span class="xy-label">أفقي (X)</span>
                            <input type="range" name="name_x" min="-220" max="220" value="{{ $initialValue('name_x', 0) }}">
                            <span class="xy-label">عمودي (Y)</span>
                            <input type="range" name="name_y" min="-120" max="220" value="{{ $initialValue('name_y', 0) }}">
                        </div>
                    </div>
                </div>

                <div class="control-group mb-3">
                    <div class="flex-row">
                        <div>
                            <label class="fw-semibold">تحريك النص الأساسي</label>
                            <span class="xy-label">أفقي (X)</span>
                            <input type="range" name="body_x" min="-120" max="120" value="{{ $initialValue('body_x', 0) }}">
                            <span class="xy-label">عمودي (Y)</span>
                            <input type="range" name="body_y" min="-80" max="160" value="{{ $initialValue('body_y', 0) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">موضع الشعار</label>
                            <span class="xy-label">أفقي (X)</span>
                            <input type="range" name="logo_x" min="-120" max="120" value="{{ $initialValue('logo_x', 0) }}">
                            <span class="xy-label">عمودي (Y)</span>
                            <input type="range" name="logo_y" min="-80" max="140" value="{{ $initialValue('logo_y', 0) }}">
                        </div>
                        <div>
                            <label class="fw-semibold">عرض الشعار</label>
                            <input type="range" name="logo_width" min="70" max="180" value="{{ $initialValue('logo_width', 110) }}">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="editor_template_id" value="{{ $editingTemplate?->id ?? 0 }}">
                <input type="hidden" name="editor_mode" value="{{ $editingTemplate ? 'edit' : ($presetData ? 'preset' : 'create') }}">
                <input type="hidden" name="title_rotation" value="{{ $initialValue('title_rotation', 0) }}">
                <input type="hidden" name="subtitle_rotation" value="{{ $initialValue('subtitle_rotation', 0) }}">
                <input type="hidden" name="name_rotation" value="{{ $initialValue('name_rotation', 0) }}">
                <input type="hidden" name="body_rotation" value="{{ $initialValue('body_rotation', 0) }}">
                <input type="hidden" name="logo_rotation" value="{{ $initialValue('logo_rotation', 0) }}">

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">{{ $editingTemplate ? 'حفظ التعديلات' : 'حفظ القالب' }}</button>
                    <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-outline">إلغاء</a>
                </div>
            </form>
        </div>

        <!-- Preset Quick Links -->
        <div class="form-card">
            <h5 class="fw-bold mb-3" style="color:var(--theme-gold);">قوالب جاهزة للتعديل</h5>
            <div class="preset-grid">
                <a href="{{ route('teacher.certificates.custom.create', ['student' => $student->id, 'preset' => 1]) }}"><i class="ri-stack-line"></i> بدء من قالب احترافي 1</a>
                <a href="{{ route('teacher.certificates.custom.create', ['student' => $student->id, 'preset' => 2]) }}"><i class="ri-stack-line"></i> بدء من قالب احترافي 2</a>
            </div>
        </div>

        <!-- Previous Templates -->
        @if($templates->isNotEmpty())
            <div class="form-card">
                <h5 class="fw-bold mb-3" style="color:var(--theme-gold);">قوالبك السابقة</h5>
                <div class="template-list">
                    @foreach($templates as $item)
                        <div class="template-list-item">
                            <span>{{ $item->name }}</span>
                            <div class="actions">
                                <a href="{{ route('teacher.certificates.custom.show', [$student, $item]) }}" class="btn btn-outline btn-sm">معاينة</a>
                                <a href="{{ route('teacher.certificates.custom.edit', [$student, $item]) }}" class="btn btn-outline btn-sm">تعديل</a>
                                <form action="{{ route('teacher.certificates.custom.destroy', [$student, $item]) }}" method="POST" style="display:inline;" class="custom-destroy-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        </div>
    </div>
</div>

<!-- Mini Preview -->
<div id="miniPreviewShell" class="mini-preview-shell">
    <div class="mini-preview-card">
        <div class="mini-preview-header">
            <span>معاينة سريعة</span>
            <span>↕</span>
        </div>
        <div class="mini-preview-body">
            <div class="mini-preview-surface" id="miniPreviewSurface">
                <div class="background-layer" id="miniPreviewBackgroundLayer"></div>
                <div class="overlay"></div>
                <div class="mini-stamp" id="miniPreviewStamp">شهادة</div>
                <div class="mini-logo-box" id="miniPreviewLogo" style="position:absolute;top:16px;right:16px;width:62px;height:62px;">
                    <img src="{{ asset('image/logono.png') }}" alt="logo">
                </div>
                <div class="mini-text-block mini-title" id="miniPreviewTitle">{{ old('title', $editingTemplate->title ?? ($presetData['title'] ?? 'شهادة إتمام')) }}</div>
                <div class="mini-text-block mini-subtitle" id="miniPreviewSubtitle" style="top:74px;">{{ old('subtitle', $editingTemplate->subtitle ?? ($presetData['subtitle'] ?? 'تقديراً لجهودكم')) }}</div>
                <div class="mini-text-block mini-name" id="miniPreviewName" style="top:116px;">{{ $student->name }}</div>
                <div class="mini-text-block mini-body" id="miniPreviewBody" style="top:140px;">{{ old('body_text', $editingTemplate->body_text ?? ($presetData['body_text'] ?? 'تمت الموافقة على استكمال البرنامج بنجاح وبتقدير عالٍ من منصة اجلال التعليمية.')) }}</div>
            </div>
        </div>
    </div>
    <div class="mini-preview-resize-handle"></div>
</div>

<script>
    const form = document.getElementById('templateForm');
    const preview = document.getElementById('livePreview');
    const editorTemplateId = form?.dataset.editorTemplateId || '0';
    const editorMode = form?.dataset.editorMode || 'create';
    const editorSignature = `${editorMode}:${editorTemplateId}`;
    const previewTitle = document.getElementById('previewTitle');
    const previewSubtitle = document.getElementById('previewSubtitle');
    const previewName = document.getElementById('previewName');
    const previewBody = document.getElementById('previewBody');
    const previewStamp = document.getElementById('previewStamp');
    const defaultRecipientName = @json($student->name);
    const previewLogo = document.getElementById('previewLogo');
    const logoPreview = document.getElementById('logoPreview');
    const miniPreviewShell = document.getElementById('miniPreviewShell');
    const initialBackgroundImage = preview?.dataset.backgroundImage || '';
    const initialLogoImage = preview?.dataset.logoImage || '';
    const backgroundLayer = document.getElementById('previewBackgroundLayer');
    const miniBackgroundLayer = document.getElementById('miniPreviewBackgroundLayer');
    const backgroundImageInput = document.querySelector('input[name="background_image"]');
    const removeBackgroundButton = document.getElementById('removeBackgroundButton');
    const removeBackgroundField = document.getElementById('removeBackgroundField');
    let activeBackgroundImage = initialBackgroundImage;
    let activeLogoImage = initialLogoImage;
    let currentEditorSignature = '';
    const miniPreviewSurface = document.getElementById('miniPreviewSurface');
    const miniPreviewTitle = document.getElementById('miniPreviewTitle');
    const miniPreviewSubtitle = document.getElementById('miniPreviewSubtitle');
    const miniPreviewName = document.getElementById('miniPreviewName');
    const miniPreviewBody = document.getElementById('miniPreviewBody');
    const miniPreviewStamp = document.getElementById('miniPreviewStamp');
    const miniPreviewLogo = document.getElementById('miniPreviewLogo');
    const miniPreviewLogoImage = miniPreviewLogo?.querySelector('img');
    const mainPreviewSection = document.getElementById('mainPreviewSection');
    const helperLines = document.createElement('div');
    helperLines.className = 'helper-lines';
    preview?.appendChild(helperLines);
    let draggingBackground = false;
    let backgroundDragState = null;
    let activeInteractiveElement = null;
    let guideState = null;
    let dragState = null;
    const guideTarget = document.getElementById('guideTarget');
    const rotationRange = document.getElementById('rotationRange');
    const rotationHint = document.getElementById('rotationHint');
    const rotationInputs = {
        title: form?.querySelector('input[name="title_rotation"]'),
        subtitle: form?.querySelector('input[name="subtitle_rotation"]'),
        recipient_name: form?.querySelector('input[name="name_rotation"]'),
        body_text: form?.querySelector('input[name="body_rotation"]'),
        logo: form?.querySelector('input[name="logo_rotation"]'),
    };

    function clearGuideLines() {
        if (helperLines) {
            helperLines.innerHTML = '';
        }
    }

    function resetSingleTemplateEditorState() {
        const nextSignature = `${form?.dataset.editorMode || 'create'}:${form?.dataset.editorTemplateId || '0'}`;
        currentEditorSignature = nextSignature;
        activeBackgroundImage = preview?.dataset.backgroundImage || '';
        activeLogoImage = preview?.dataset.logoImage || '';

        if (backgroundImageInput) {
            backgroundImageInput.value = '';
        }
        if (removeBackgroundField) {
            removeBackgroundField.value = '0';
        }
        clearGuideLines();
        updatePreview();
    }

    function showGuideLines(type, value) {
        clearGuideLines();
        if (!preview || !helperLines) return;
        const rect = preview.getBoundingClientRect();
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        if (type === 'center-vertical' || type === 'center') {
            const line = document.createElement('div');
            line.className = 'helper-line vertical';
            line.style.left = `${centerX}px`;
            helperLines.appendChild(line);
        }
        if (type === 'center-horizontal' || type === 'center') {
            const line = document.createElement('div');
            line.className = 'helper-line horizontal';
            line.style.top = `${centerY}px`;
            helperLines.appendChild(line);
        }
        if (type === 'snap-horizontal') {
            const line = document.createElement('div');
            line.className = 'helper-line horizontal';
            line.style.top = `${centerY}px`;
            helperLines.appendChild(line);
        }
        if (type === 'snap-vertical') {
            const line = document.createElement('div');
            line.className = 'helper-line vertical';
            line.style.left = `${centerX}px`;
            helperLines.appendChild(line);
        }
        if (typeof value === 'number') {
            const line = document.createElement('div');
            line.className = 'helper-line diagonal';
            line.style.left = '0';
            line.style.top = `${rect.height / 2}px`;
            line.style.width = `${rect.width}px`;
            line.style.transform = `rotate(${value}deg)`;
            helperLines.appendChild(line);
        }
    }

    function getPositionInputs(role) {
        switch (role) {
            case 'title':
                return { x: form?.querySelector('input[name="title_x"]'), y: form?.querySelector('input[name="title_y"]') };
            case 'subtitle':
                return { x: form?.querySelector('input[name="subtitle_x"]'), y: form?.querySelector('input[name="subtitle_y"]') };
            case 'recipient_name':
                return { x: form?.querySelector('input[name="name_x"]'), y: form?.querySelector('input[name="name_y"]') };
            case 'body_text':
                return { x: form?.querySelector('input[name="body_x"]'), y: form?.querySelector('input[name="body_y"]') };
            case 'logo':
                return { x: form?.querySelector('input[name="logo_x"]'), y: form?.querySelector('input[name="logo_y"]') };
            default:
                return { x: null, y: null };
        }
    }

    function updateRotationControl() {
        const target = guideTarget?.value || 'title';
        const input = rotationInputs[target];
        const value = Number(input?.value || 0);
        if (rotationRange) {
            rotationRange.value = value;
        }
        if (rotationHint) {
            rotationHint.innerText = `${value}°`;
        }
    }

    function setRotationValue(target, value) {
        const input = rotationInputs[target];
        if (input) {
            input.value = value;
        }
        updatePreview();
        updateRotationControl();
    }

    function applyGuideAction(action) {
        const target = guideTarget?.value || activeInteractiveElement?.dataset.role || 'title';
        const inputs = getPositionInputs(target);
        const previewRect = preview?.getBoundingClientRect();
        const baseTop = target === 'title' ? 140 : target === 'subtitle' ? 210 : target === 'recipient_name' ? 300 : target === 'body_text' ? 370 : 30;
        if (inputs.x && inputs.y) {
            switch (action) {
                case 'center-h':
                    inputs.x.value = 0;
                    showGuideLines('center-vertical');
                    break;
                case 'center-v':
                    inputs.y.value = Math.round((previewRect?.height || 600) / 2 - baseTop);
                    showGuideLines('center-horizontal');
                    break;
                case 'snap-horizontal':
                    inputs.y.value = Math.round((previewRect?.height || 600) / 2 - baseTop);
                    showGuideLines('snap-horizontal');
                    break;
                case 'snap-vertical':
                    inputs.x.value = 0;
                    showGuideLines('snap-vertical');
                    break;
                case 'rotate-45':
                    setRotationValue(target, 45);
                    showGuideLines('rotation', 45);
                    return;
                case 'rotate-90':
                    setRotationValue(target, 90);
                    showGuideLines('rotation', 90);
                    return;
            }
        }
        updatePreview();
    }

    function updatePreview() {
        const values = new FormData(form);
        const title = values.get('title') || 'شهادة إتمام';
        const subtitle = values.get('subtitle') || 'تقديراً لجهودكم';
        const recipientName = values.get('recipient_name') || defaultRecipientName;
        const body = values.get('body_text') || 'تمت الموافقة على استكمال البرنامج بنجاح وبتقدير عالٍ من منصة اجلال التعليمية.';
        const primary = values.get('primary_color') || '#C4963A';
        const secondary = values.get('secondary_color') || '#A07A28';
        const accent = values.get('accent_color') || '#D4A84B';
        const bgType = values.get('background_type') || 'gradient';
        const fontFamily = values.get('font_family') || 'Cairo';
        const textAlign = values.get('text_align') || 'center';
        const titleX = Number(values.get('title_x') || 0);
        const titleY = Number(values.get('title_y') || 0);
        const subtitleX = Number(values.get('subtitle_x') || 0);
        const subtitleY = Number(values.get('subtitle_y') || 0);
        const nameX = Number(values.get('name_x') || 0);
        const nameY = Number(values.get('name_y') || 0);
        const bodyX = Number(values.get('body_x') || 0);
        const bodyY = Number(values.get('body_y') || 0);
        const logoX = values.get('logo_x') || 0;
        const logoY = values.get('logo_y') || 0;
        const logoWidth = values.get('logo_width') || 110;
        const stampSize = values.get('stamp_size') || 120;
        const overlayOpacity = (values.get('overlay_opacity') || 15) / 100;
        const borderRadius = values.get('border_radius') || 30;
        const titleSize = values.get('title_size') || 38;
        const subtitleSize = values.get('subtitle_size') || 20;
        const nameSize = values.get('name_size') || 32;
        const bodySize = values.get('body_size') || 18;
        const titleColor = values.get('title_color') || '#ffffff';
        const subtitleColor = values.get('subtitle_color') || '#ffffff';
        const nameColor = values.get('name_color') || '#ffffff';
        const bodyColor = values.get('body_color') || '#ffffff';
        const titleRotation = Number(values.get('title_rotation') || 0);
        const subtitleRotation = Number(values.get('subtitle_rotation') || 0);
        const nameRotation = Number(values.get('name_rotation') || 0);
        const bodyRotation = Number(values.get('body_rotation') || 0);
        const logoRotation = Number(values.get('logo_rotation') || 0);
        const backgroundPositionX = values.get('background_position_x') || 50;
        const backgroundPositionY = values.get('background_position_y') || 50;
        const backgroundSize = values.get('background_size') || 100;
        const showLogo = values.get('show_logo') === '1';
        const showStamp = values.get('show_stamp') === '1';

        if (bgType === 'image' && activeBackgroundImage) {
            backgroundLayer.style.backgroundImage = `url(${activeBackgroundImage})`;
            backgroundLayer.style.backgroundPosition = `${backgroundPositionX}% ${backgroundPositionY}%`;
            backgroundLayer.style.backgroundSize = `${backgroundSize}% ${backgroundSize}%`;
            backgroundLayer.style.backgroundRepeat = 'no-repeat';
            backgroundLayer.style.backgroundColor = 'transparent';
            backgroundLayer.style.opacity = '1';
            preview.style.background = 'transparent';
        } else if (bgType === 'solid') {
            backgroundLayer.style.backgroundImage = 'none';
            backgroundLayer.style.backgroundColor = primary;
            backgroundLayer.style.opacity = '1';
            preview.style.background = 'transparent';
        } else {
            backgroundLayer.style.backgroundImage = `linear-gradient(135deg, ${primary} 0%, ${secondary} 50%, ${accent} 100%)`;
            backgroundLayer.style.backgroundColor = 'transparent';
            backgroundLayer.style.opacity = '1';
            preview.style.background = 'transparent';
        }
        preview.style.borderRadius = `${borderRadius}px`;
        preview.querySelector('.overlay').style.opacity = overlayOpacity;

        previewTitle.innerText = title;
        previewSubtitle.innerText = subtitle;
        previewName.innerText = recipientName;
        previewBody.innerText = body;
        previewTitle.style.fontSize = `${titleSize}px`;
        previewSubtitle.style.fontSize = `${subtitleSize}px`;
        previewName.style.fontSize = `${nameSize}px`;
        previewBody.style.fontSize = `${bodySize}px`;
        previewTitle.style.left = `calc(50% + ${titleX}px)`;
        previewSubtitle.style.left = `calc(50% + ${subtitleX}px)`;
        previewName.style.left = `calc(50% + ${nameX}px)`;
        previewBody.style.left = `calc(50% + ${bodyX}px)`;
        previewTitle.style.top = `${titleY + 140}px`;
        previewSubtitle.style.top = `${subtitleY + 210}px`;
        previewName.style.top = `${nameY + 300}px`;
        previewBody.style.top = `${bodyY + 370}px`;
        previewTitle.style.transform = `translateX(-50%) rotate(${titleRotation}deg)`;
        previewSubtitle.style.transform = `translateX(-50%) rotate(${subtitleRotation}deg)`;
        previewName.style.transform = `translateX(-50%) rotate(${nameRotation}deg)`;
        previewBody.style.transform = `translateX(-50%) rotate(${bodyRotation}deg)`;
        previewTitle.style.color = titleColor;
        previewSubtitle.style.color = subtitleColor;
        previewName.style.color = nameColor;
        previewBody.style.color = bodyColor;
        previewTitle.style.fontFamily = `${fontFamily}, sans-serif`;
        previewSubtitle.style.fontFamily = `${fontFamily}, sans-serif`;
        previewName.style.fontFamily = `${fontFamily}, sans-serif`;
        previewBody.style.fontFamily = `${fontFamily}, sans-serif`;
        previewTitle.style.textAlign = textAlign;
        previewSubtitle.style.textAlign = textAlign;
        previewName.style.textAlign = textAlign;
        previewBody.style.textAlign = textAlign;
        previewTitle.style.right = 'auto';
        previewSubtitle.style.right = 'auto';
        previewName.style.right = 'auto';
        previewBody.style.right = 'auto';

        if (previewStamp) {
            previewStamp.style.display = showStamp ? 'grid' : 'none';
            previewStamp.style.width = `${stampSize}px`;
            previewStamp.style.height = `${stampSize}px`;
        }
        if (previewLogo) {
            previewLogo.style.display = showLogo ? 'grid' : 'none';
            previewLogo.style.right = `${Number(logoX) + 30}px`;
            previewLogo.style.top = `${Number(logoY) + 30}px`;
            previewLogo.style.width = `${logoWidth}px`;
            previewLogo.style.height = `${logoWidth}px`;
            previewLogo.style.transform = `rotate(${logoRotation}deg)`;
        }
        if (logoPreview) {
            logoPreview.src = activeLogoImage || '{{ asset('image/logono.png') }}';
        }
        if (miniPreviewLogoImage) {
            miniPreviewLogoImage.src = activeLogoImage || '{{ asset('image/logono.png') }}';
        }

        if (miniPreviewSurface) {
            if (bgType === 'image' && activeBackgroundImage) {
                miniBackgroundLayer.style.backgroundImage = `url(${activeBackgroundImage})`;
                miniBackgroundLayer.style.backgroundPosition = `${backgroundPositionX}% ${backgroundPositionY}%`;
                miniBackgroundLayer.style.backgroundSize = `${backgroundSize}% ${backgroundSize}%`;
                miniBackgroundLayer.style.backgroundRepeat = 'no-repeat';
                miniBackgroundLayer.style.backgroundColor = 'transparent';
                miniBackgroundLayer.style.opacity = '1';
                miniPreviewSurface.style.background = 'transparent';
            } else if (bgType === 'solid') {
                miniBackgroundLayer.style.backgroundImage = 'none';
                miniBackgroundLayer.style.backgroundColor = primary;
                miniBackgroundLayer.style.opacity = '1';
                miniPreviewSurface.style.background = 'transparent';
            } else {
                miniBackgroundLayer.style.backgroundImage = `linear-gradient(135deg, ${primary} 0%, ${secondary} 50%, ${accent} 100%)`;
                miniBackgroundLayer.style.backgroundColor = 'transparent';
                miniBackgroundLayer.style.opacity = '1';
                miniPreviewSurface.style.background = 'transparent';
            }
            miniPreviewSurface.querySelector('.overlay').style.opacity = overlayOpacity;
            miniPreviewTitle.innerText = title;
            miniPreviewSubtitle.innerText = subtitle;
            miniPreviewName.innerText = recipientName;
            miniPreviewBody.innerText = body;
            miniPreviewTitle.style.fontSize = `${Math.max(18, titleSize * 0.55)}px`;
            miniPreviewSubtitle.style.fontSize = `${Math.max(12, subtitleSize * 0.6)}px`;
            miniPreviewName.style.fontSize = `${Math.max(14, nameSize * 0.55)}px`;
            miniPreviewBody.style.fontSize = `${Math.max(10, bodySize * 0.6)}px`;
            miniPreviewTitle.style.left = `calc(50% + ${titleX}px)`;
            miniPreviewSubtitle.style.left = `calc(50% + ${subtitleX}px)`;
            miniPreviewName.style.left = `calc(50% + ${nameX}px)`;
            miniPreviewBody.style.left = `calc(50% + ${bodyX}px)`;
            miniPreviewTitle.style.top = `${Math.max(22, Number(titleY) * 0.55 + 54)}px`;
            miniPreviewSubtitle.style.top = `${Math.max(56, Number(subtitleY) * 0.55 + 74)}px`;
            miniPreviewName.style.top = `${Math.max(94, Number(nameY) * 0.55 + 102)}px`;
            miniPreviewBody.style.top = `${Math.max(120, Number(bodyY) * 0.55 + 126)}px`;
            miniPreviewTitle.style.transform = `translateX(-50%) rotate(${titleRotation}deg)`;
            miniPreviewSubtitle.style.transform = `translateX(-50%) rotate(${subtitleRotation}deg)`;
            miniPreviewName.style.transform = `translateX(-50%) rotate(${nameRotation}deg)`;
            miniPreviewBody.style.transform = `translateX(-50%) rotate(${bodyRotation}deg)`;
            miniPreviewTitle.style.color = titleColor;
            miniPreviewSubtitle.style.color = subtitleColor;
            miniPreviewName.style.color = nameColor;
            miniPreviewBody.style.color = bodyColor;
            miniPreviewTitle.style.fontFamily = `${fontFamily}, sans-serif`;
            miniPreviewSubtitle.style.fontFamily = `${fontFamily}, sans-serif`;
            miniPreviewName.style.fontFamily = `${fontFamily}, sans-serif`;
            miniPreviewBody.style.fontFamily = `${fontFamily}, sans-serif`;
            miniPreviewTitle.style.textAlign = textAlign;
            miniPreviewSubtitle.style.textAlign = textAlign;
            miniPreviewName.style.textAlign = textAlign;
            miniPreviewBody.style.textAlign = textAlign;
            miniPreviewTitle.style.right = 'auto';
            miniPreviewSubtitle.style.right = 'auto';
            miniPreviewName.style.right = 'auto';
            miniPreviewBody.style.right = 'auto';
            if (miniPreviewStamp) {
                miniPreviewStamp.style.display = showStamp ? 'grid' : 'none';
                miniPreviewStamp.style.width = `${Math.max(44, stampSize * 0.55)}px`;
                miniPreviewStamp.style.height = `${Math.max(44, stampSize * 0.55)}px`;
            }
            if (miniPreviewLogo) {
                miniPreviewLogo.style.display = showLogo ? 'grid' : 'none';
                miniPreviewLogo.style.right = `${Math.max(10, Number(logoX) * 0.55 + 14)}px`;
                miniPreviewLogo.style.top = `${Math.max(10, Number(logoY) * 0.55 + 14)}px`;
                miniPreviewLogo.style.width = `${Math.max(36, logoWidth * 0.55)}px`;
                miniPreviewLogo.style.height = `${Math.max(36, logoWidth * 0.55)}px`;
                miniPreviewLogo.style.transform = `rotate(${logoRotation}deg)`;
            }
        }
    }

    function updateMiniPreviewVisibility() {
        if (!mainPreviewSection || !miniPreviewShell) return;
        const rect = mainPreviewSection.getBoundingClientRect();
        const visible = rect.bottom < 140 || rect.top > window.innerHeight - 140;
        miniPreviewShell.classList.toggle('is-visible', visible);
    }

    const header = miniPreviewShell?.querySelector('.mini-preview-header');
    const resizeHandle = miniPreviewShell?.querySelector('.mini-preview-resize-handle');

    header?.addEventListener('pointerdown', (event) => {
        if (!miniPreviewShell) return;
        dragState = {
            type: 'move',
            startX: event.clientX,
            startY: event.clientY,
            startLeft: parseFloat(getComputedStyle(miniPreviewShell).left) || 0,
            startTop: parseFloat(getComputedStyle(miniPreviewShell).top) || 0,
        };
        miniPreviewShell.setPointerCapture(event.pointerId);
    });

    resizeHandle?.addEventListener('pointerdown', (event) => {
        if (!miniPreviewShell) return;
        dragState = {
            type: 'resize',
            startX: event.clientX,
            startY: event.clientY,
            startWidth: miniPreviewShell.offsetWidth,
            startHeight: miniPreviewShell.offsetHeight,
        };
        miniPreviewShell.setPointerCapture(event.pointerId);
    });

    preview?.addEventListener('pointerdown', (event) => {
        if (event.target.closest('.draggable-element')) return;
        const backgroundType = form.querySelector('select[name="background_type"]');
        if (!preview || !activeBackgroundImage || !backgroundType || backgroundType.value !== 'image') return;
        draggingBackground = true;
        backgroundDragState = {
            startX: event.clientX,
            startY: event.clientY,
            startPositionX: Number(form.querySelector('input[name="background_position_x"]').value || 50),
            startPositionY: Number(form.querySelector('input[name="background_position_y"]').value || 50),
        };
        preview.setPointerCapture(event.pointerId);
    });

    document.querySelectorAll('.draggable-element').forEach(element => {
        element.addEventListener('pointerdown', (event) => {
            event.preventDefault();
            activeInteractiveElement = element;
            document.querySelectorAll('.draggable-element').forEach(item => item.classList.toggle('selected', item === element));
            const role = element.dataset.role || 'title';
            const inputs = getPositionInputs(role);
            dragState = {
                type: 'element',
                role,
                startX: event.clientX,
                startY: event.clientY,
                startXValue: Number(inputs.x?.value || 0),
                startYValue: Number(inputs.y?.value || 0),
            };
            element.classList.add('dragging');
            element.setPointerCapture(event.pointerId);
            showGuideLines('center');
        });

        element.addEventListener('pointerup', () => {
            element.classList.remove('dragging');
            clearGuideLines();
        });
    });

    preview?.addEventListener('pointermove', (event) => {
        if (!draggingBackground || !backgroundDragState || !preview) return;
        const rect = preview.getBoundingClientRect();
        const deltaX = ((event.clientX - backgroundDragState.startX) / rect.width) * 100;
        const deltaY = ((event.clientY - backgroundDragState.startY) / rect.height) * 100;
        const nextX = Math.min(100, Math.max(0, backgroundDragState.startPositionX + deltaX));
        const nextY = Math.min(100, Math.max(0, backgroundDragState.startPositionY + deltaY));
        const xInput = form.querySelector('input[name="background_position_x"]');
        const yInput = form.querySelector('input[name="background_position_y"]');
        if (xInput) xInput.value = Math.round(nextX);
        if (yInput) yInput.value = Math.round(nextY);
        updatePreview();
    });

    preview?.addEventListener('pointerup', () => {
        draggingBackground = false;
        backgroundDragState = null;
    });

    preview?.addEventListener('pointerleave', () => {
        if (draggingBackground) return;
        draggingBackground = false;
        backgroundDragState = null;
    });

    document.addEventListener('pointermove', (event) => {
        if (!dragState) return;
        if (dragState.type === 'move' && miniPreviewShell) {
            const nextLeft = Math.min(Math.max(dragState.startLeft + event.clientX - dragState.startX, 8), window.innerWidth - miniPreviewShell.offsetWidth - 8);
            const nextTop = Math.min(Math.max(dragState.startTop + event.clientY - dragState.startY, 8), window.innerHeight - miniPreviewShell.offsetHeight - 8);
            miniPreviewShell.style.left = `${nextLeft}px`;
            miniPreviewShell.style.top = `${nextTop}px`;
            miniPreviewShell.style.right = 'auto';
            miniPreviewShell.style.bottom = 'auto';
        } else if (dragState.type === 'resize' && miniPreviewShell) {
            const nextWidth = Math.min(Math.max(dragState.startWidth + event.clientX - dragState.startX, 240), 380);
            const nextHeight = Math.min(Math.max(dragState.startHeight + event.clientY - dragState.startY, 240), 460);
            miniPreviewShell.style.width = `${nextWidth}px`;
            miniPreviewShell.style.height = `${nextHeight}px`;
        } else if (dragState.type === 'element' && preview) {
            const deltaX = event.clientX - dragState.startX;
            const deltaY = event.clientY - dragState.startY;
            const nextX = dragState.startXValue + deltaX;
            const nextY = dragState.startYValue + deltaY;
            const snapThreshold = 18;
            const baseTop = dragState.role === 'title' ? 140 : dragState.role === 'subtitle' ? 210 : dragState.role === 'recipient_name' ? 300 : dragState.role === 'body_text' ? 370 : 30;
            const centerY = (preview.getBoundingClientRect().height / 2) - baseTop;
            const snappedX = Math.abs(nextX) < snapThreshold ? 0 : nextX;
            const snappedY = Math.abs(nextY - centerY) < snapThreshold ? centerY : nextY;
            const inputs = getPositionInputs(dragState.role);
            if (inputs.x) inputs.x.value = Math.round(snappedX);
            if (inputs.y) inputs.y.value = Math.round(snappedY);
            if (Math.abs(snappedX) < snapThreshold && Math.abs(snappedY - centerY) < snapThreshold) {
                showGuideLines('center');
            } else if (Math.abs(snappedX) < snapThreshold) {
                showGuideLines('center-vertical');
            } else if (Math.abs(snappedY - centerY) < snapThreshold) {
                showGuideLines('center-horizontal');
            } else {
                clearGuideLines();
            }
            updatePreview();
        }
    });

    document.addEventListener('pointerup', () => {
        dragState = null;
        draggingBackground = false;
        backgroundDragState = null;
        clearGuideLines();
    });

    function syncEditablePreviewText(element, fieldName) {
        if (!element) return;
        element.addEventListener('input', () => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.value = element.innerText.replace(/\n/g, ' ').trim();
            }
            updatePreview();
        });
        element.addEventListener('blur', () => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.value = element.innerText.replace(/\n/g, ' ').trim();
            }
            updatePreview();
        });
    }

    syncEditablePreviewText(previewTitle, 'title');
    syncEditablePreviewText(previewSubtitle, 'subtitle');
    syncEditablePreviewText(previewName, 'recipient_name');
    syncEditablePreviewText(previewBody, 'body_text');

    // Enable editing only on double-click to allow single-click dragging without selecting text
    document.querySelectorAll('.text-block').forEach(el => {
        el.addEventListener('dblclick', (e) => {
            e.stopPropagation();
            el.contentEditable = true;
            el.focus();
            const range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        });
        el.addEventListener('blur', () => {
            el.contentEditable = false;
            const field = el.dataset.field;
            const input = form.querySelector(`[name="${field}"]`);
            if (input) input.value = el.innerText.replace(/\n/g, ' ').trim();
            updatePreview();
        });
        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                el.blur();
            }
        });
    });

    Array.from(form.elements).forEach(input => input.addEventListener('input', updatePreview));
    Array.from(form.elements).forEach(input => input.addEventListener('change', updatePreview));
    guideTarget?.addEventListener('change', updateRotationControl);
    rotationRange?.addEventListener('input', () => {
        const target = guideTarget?.value || 'title';
        setRotationValue(target, rotationRange.value);
        if (rotationHint) {
            rotationHint.innerText = `${rotationRange.value}°`;
        }
    });
    document.querySelectorAll('[data-guide-action]').forEach(button => {
        button.addEventListener('click', () => applyGuideAction(button.dataset.guideAction));
    });

    document.querySelector('input[name="logo_image"]').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            activeLogoImage = e.target.result;
            if (logoPreview) {
                logoPreview.src = activeLogoImage;
            }
            if (miniPreviewLogoImage) {
                miniPreviewLogoImage.src = activeLogoImage;
            }
            updatePreview();
        };
        reader.readAsDataURL(file);
    });

    document.querySelector('input[name="background_image"]').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            activeBackgroundImage = e.target.result;
            if (removeBackgroundField) {
                removeBackgroundField.value = '0';
            }
            const backgroundType = form.querySelector('select[name="background_type"]');
            if (backgroundType) {
                backgroundType.value = 'image';
            }
            updatePreview();
        };
        reader.readAsDataURL(file);
    });

    removeBackgroundButton?.addEventListener('click', () => {
        activeBackgroundImage = '';
        if (backgroundImageInput) {
            backgroundImageInput.value = '';
        }
        if (removeBackgroundField) {
            removeBackgroundField.value = '1';
        }
        const backgroundType = form.querySelector('select[name="background_type"]');
        if (backgroundType) {
            backgroundType.value = 'gradient';
        }
        updatePreview();
    });

    window.addEventListener('scroll', updateMiniPreviewVisibility, { passive: true });
    window.addEventListener('resize', updateMiniPreviewVisibility);
    window.addEventListener('pageshow', resetSingleTemplateEditorState);
    resetSingleTemplateEditorState();
    updatePreview();
    updateMiniPreviewVisibility();

    document.querySelectorAll('.custom-destroy-form').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            if (!confirm('هل أنت متأكد أنك تريد حذف هذا القالب؟ هذه العملية لا يمكن التراجع عنها.')) {
                e.preventDefault();
            }
        });
    });
</script>
    @include('components.account-theme-foot')
</body>
</html>